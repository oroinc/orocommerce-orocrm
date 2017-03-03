<?php

namespace Oro\Bridge\CustomerAccount\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ImportExportBundle\Event\AfterEntityPageLoadedEvent;
use Oro\Bundle\ImportExportBundle\Event\Events;
use Oro\Bundle\ImportExportBundle\Event\LoadEntityRulesAndBackendHeadersEvent;
use Oro\Bundle\ImportExportBundle\Event\NormalizeEntityEvent;
use Oro\Bundle\SalesBundle\Entity\Customer as SalesCustomer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\SalesBundle\Entity\Repository\CustomerRepository;

class CustomerAccountImportExportSubscriber implements EventSubscriberInterface
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var string
     */
    private $customerClassName;

    /**
     * @var Account[]
     */
    private $customerAccounts = [];

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param string $customerClassName
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        $customerClassName
    ) {
        $this->doctrineHelper = $doctrineHelper;
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
        ];
    }

    /**
     * @param AfterEntityPageLoadedEvent $event
     */
    public function updateEntityResults(AfterEntityPageLoadedEvent $event)
    {
        $rows = $event->getRows();
        if (empty($rows) || !is_a($rows[0], $this->customerClassName)) {
            return;
        }

        $this->loadCustomerAccounts($rows);
    }

    /**
     * @param NormalizeEntityEvent $event
     */
    public function normalizeEntity(NormalizeEntityEvent $event)
    {
        if (!$event->isFullData() || !is_a($event->getObject(), $this->customerClassName)) {
            return;
        }

        /** @var Customer $customer */
        $customer = $event->getObject();
        $event->setResultField(
            'account',
            $this->normalizeCustomerAccount($customer)
        );
    }

    /**
     * @param LoadEntityRulesAndBackendHeadersEvent $event
     */
    public function loadEntityRulesAndBackendHeaders(LoadEntityRulesAndBackendHeadersEvent $event)
    {
        if (!$event->isFullData() || $event->getEntityName() !== $this->customerClassName) {
            return;
        }

        $event->addHeader([
            'value' => sprintf('account%sname', $event->getConvertDelimiter()),
            'order' => 300,
        ]);

        $event->setRule('Account', [
            'value' => sprintf('account%sname', $event->getConvertDelimiter()),
            'order' => 300,
        ]);
    }

    /**
     * @return CustomerRepository
     */
    private function getCustomerRepository()
    {
        return $this->doctrineHelper->getEntityRepository(SalesCustomer::class);
    }

    /**
     * @param Customer[] $rows
     */
    private function loadCustomerAccounts(array $rows)
    {
        $field = AccountCustomerManager::getCustomerTargetField($this->customerClassName);

        /** @var Customer $customer */
        foreach ($rows as $customer) {
            $salesAccount = $this->getCustomerRepository()->getCustomerByTarget($customer->getId(), $field);

            if ($salesAccount) {
                $this->customerAccounts[$customer->getId()] = $salesAccount->getAccount();
            }
        }
    }

    /**
     * @param Customer $customer
     * @return string[]
     */
    private function normalizeCustomerAccount(Customer $customer)
    {
        if (!isset($this->customerAccounts[$customer->getId()])) {
            return ['name' => ''];
        }

        return [
            'name' => $this->customerAccounts[$customer->getId()]->getName()
        ];
    }
}
