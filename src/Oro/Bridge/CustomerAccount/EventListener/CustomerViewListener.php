<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;

class CustomerViewListener
{
    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /** @var RequestStack */
    protected $requestStack;

    /** @var string */
    protected $entityClass;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var  ConfigManager */
    protected $configManager;

    /**
     * @param string $entityClass
     * @param DoctrineHelper $doctrineHelper
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     * @param ConfigManager $configManager
     *
     */
    public function __construct(
        $entityClass,
        DoctrineHelper $doctrineHelper,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        ConfigManager $configManager
    ) {
        $this->doctrineHelper = $doctrineHelper;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->entityClass = $entityClass;
        $this->configManager = $configManager;
    }

    /**
     * @return null|object
     */
    protected function getEntityFromRequest()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        $customerId = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if (false === $customerId) {
            return null;
        }

        return $this->doctrineHelper->getEntityReference($this->entityClass, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function onView(BeforeListRenderEvent $event)
    {
        /** @var Customer $customer */
        $customer = $this->getEntityFromRequest();
        if (!$customer) {
            return;
        }

        $template = $event->getEnvironment()->render(
            'OroCustomerAccountBridgeBundle:Customer:view.html.twig',
            ['entity' => $customer]
        );

        $title = $this->configManager->get('oro.customer.account.lifetime.label');
        $title = $this->translator->trans($title);

        $scrollData = $event->getScrollData();
        $blockId = $scrollData->addBlock($title);
        $subBlockId = $scrollData->addSubBlock($blockId);
        $scrollData->addSubBlockData($blockId, $subBlockId, $template);
    }
}
