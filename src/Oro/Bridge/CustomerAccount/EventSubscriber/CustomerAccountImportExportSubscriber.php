<?php

namespace Oro\Bridge\CustomerAccount\EventSubscriber;

use Oro\Bridge\CustomerAccount\Helper\CustomerAccountImportExportHelper;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\ImportExportBundle\Event\AfterEntityPageLoadedEvent;
use Oro\Bundle\ImportExportBundle\Event\Events;
use Oro\Bundle\ImportExportBundle\Event\LoadEntityRulesAndBackendHeadersEvent;
use Oro\Bundle\ImportExportBundle\Event\LoadTemplateFixturesEvent;
use Oro\Bundle\ImportExportBundle\Event\NormalizeEntityEvent;
use Oro\Bundle\ImportExportBundle\Event\StrategyEvent;
use Oro\Bundle\ImportExportBundle\EventListener\ImportExportHeaderModifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Manages extra operations during import/export for customer account.
 */
class CustomerAccountImportExportSubscriber implements EventSubscriberInterface
{
    /**
     * @var CustomerAccountImportExportHelper
     */
    private $customerAccountImportExportHelper;

    /**
     * @var string
     */
    private $customerClassName;

    /**
     * @var Account[]
     */
    private $customerAccounts = [];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     * @param CustomerAccountImportExportHelper $customerAccountImportExportHelper
     * @param string $customerClassName
     */
    public function __construct(
        TranslatorInterface $translator,
        CustomerAccountImportExportHelper $customerAccountImportExportHelper,
        $customerClassName
    ) {
        $this->translator = $translator;
        $this->customerAccountImportExportHelper = $customerAccountImportExportHelper;
        $this->customerClassName = $customerClassName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::AFTER_ENTITY_PAGE_LOADED => 'updateEntityResults',
            Events::AFTER_NORMALIZE_ENTITY => 'normalizeEntity',
            Events::AFTER_LOAD_ENTITY_RULES_AND_BACKEND_HEADERS => 'loadEntityRulesAndBackendHeaders',
            Events::AFTER_LOAD_TEMPLATE_FIXTURES => 'addAccountToCustomers',
            StrategyEvent::PROCESS_AFTER => 'processAfter',
        ];
    }

    public function updateEntityResults(AfterEntityPageLoadedEvent $event)
    {
        $rows = $event->getRows();
        if (empty($rows) || !is_a($rows[0], $this->customerClassName)) {
            return;
        }

        $this->loadCustomerAccounts($rows);
    }

    public function normalizeEntity(NormalizeEntityEvent $event)
    {
        if (!$event->isFullData() || !is_a($event->getObject(), $this->customerClassName)) {
            return;
        }

        /** @var Customer $customer */
        $customer = $event->getObject();
        $event->setResultFieldValue(
            'account',
            $this->normalizeCustomerAccount($customer)
        );
    }

    public function processAfter(StrategyEvent $event)
    {
        /** @var Customer $entity */
        $entity = $event->getEntity();
        $data = $event->getContext()->getValue('itemData');

        if (!is_a($entity, $this->customerClassName)) {
            return;
        }

        if (!isset($data['account'], $data['account']['id']) || empty($data['account']['id'])) {
            return;
        }

        $customerAccount = $this->customerAccountImportExportHelper->fetchAccount($data['account']['id']);

        if ($customerAccount) {
            $this->customerAccountImportExportHelper->assignAccount($entity, $customerAccount);
        } else {
            $event->setEntity(null);
            $event->getContext()->addError(
                $this->translator->trans(
                    'oro.account.import_export.account_doesnt_exists',
                    ['%customer_name%' => $entity->getName()]
                )
            );
        }
    }

    public function loadEntityRulesAndBackendHeaders(LoadEntityRulesAndBackendHeadersEvent $event)
    {
        if (!$event->isFullData() || $event->getEntityName() !== $this->customerClassName) {
            return;
        }

        ImportExportHeaderModifier::addHeader(
            $event,
            sprintf('account%sid', $event->getConvertDelimiter()),
            'Account Id',
            300
        );
    }

    public function addAccountToCustomers(LoadTemplateFixturesEvent $event)
    {
        foreach ($event->getEntities() as $customerData) {
            foreach ($customerData as $customer) {
                /** @var Customer $customer */
                $customer = $customer['entity'];

                if (!$customer instanceof Customer) {
                    continue;
                }

                $this->customerAccounts[$customer->getId()] = (new Account())->setId(1);
            }
        }
    }

    /**
     * @param Customer[] $rows
     */
    private function loadCustomerAccounts(array $rows)
    {
        $this->customerAccounts = $this->customerAccounts
            + $this->customerAccountImportExportHelper->loadCustomerAccounts($rows);
    }

    /**
     * @param Customer $customer
     * @return string[]
     */
    private function normalizeCustomerAccount(Customer $customer)
    {
        if (!isset($this->customerAccounts[$customer->getId()])) {
            return ['id' => ''];
        }

        $id = $this->customerAccounts[$customer->getId()]->getId();
        unset($this->customerAccounts[$customer->getId()]);

        return [
            'id' => $id
        ];
    }
}
