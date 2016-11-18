<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;

class AccountFormViewListener
{
    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /** @var RequestStack */
    protected $requestStack;

    /** @var string */
    protected $entityClass;

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param RequestStack $requestStack
     * @param string $entityClass
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        RequestStack $requestStack,
        $entityClass
    ) {
        $this->doctrineHelper = $doctrineHelper;
        $this->requestStack = $requestStack;
        $this->entityClass = $entityClass;
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

        $accountId = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if (false === $accountId) {
            return null;
        }

        return $this->doctrineHelper->getEntityReference($this->entityClass, $accountId);
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
            'OroCustomerAccountBridgeBundle:Account:account_view.html.twig',
            ['entity' => $customer]
        );
        $event->getScrollData()->addSubBlockData(0, 0, $template);
    }

    /**
     * @param BeforeListRenderEvent $event
     */
    public function onEdit(BeforeListRenderEvent $event)
    {
        $template = $event->getEnvironment()->render(
            'OroCustomerAccountBridgeBundle:Account:account_update.html.twig',
            ['form' => $event->getFormView()]
        );
        $event->getScrollData()->addSubBlockData(0, 0, $template);
    }
}
