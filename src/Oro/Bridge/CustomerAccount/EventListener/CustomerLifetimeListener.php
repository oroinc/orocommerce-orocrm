<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bundle\CurrencyBundle\Converter\RateConverterInterface;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\PaymentBundle\Entity\PaymentStatus;
use Oro\Bundle\PaymentBundle\Manager\PaymentStatusManager;
use Oro\Bundle\PaymentBundle\Provider\PaymentStatusProvider;
use Oro\Component\DependencyInjection\ServiceLink;

/**
 * Calculates and keeps actual customer lifetime value.
 */
class CustomerLifetimeListener
{
    /** @var UnitOfWork */
    protected $uow;

    /** @var EntityManager */
    protected $em;

    /** @var LifetimeProcessor */
    protected $lifetimeProcessor;

    /** @var Customer[] */
    protected $queued = [];

    /** @var bool */
    protected $isInProgress = false;

    /** @var RateConverterInterface  */
    protected $rateConverter;

    private PaymentStatusManager $paymentStatusManager;

    /** @var DoctrineHelper */
    private $doctrineHelper;

    public function __construct(
        ServiceLink $rateConverterLink,
        LifetimeProcessor $lifetimeProcessor,
        PaymentStatusManager $paymentStatusManager,
        DoctrineHelper $doctrineHelper
    ) {
        $this->rateConverter = $rateConverterLink->getService();
        $this->lifetimeProcessor = $lifetimeProcessor;
        $this->paymentStatusManager = $paymentStatusManager;
        $this->doctrineHelper = $doctrineHelper;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $this->initializeFromEventArgs($args);

        $entities = array_merge(
            $this->uow->getScheduledEntityInsertions(),
            $this->uow->getScheduledEntityDeletions(),
            $this->uow->getScheduledEntityUpdates()
        );

        $orders = $paymentStatuses = [];
        foreach ($entities as $entity) {
            $entityClass = $this->doctrineHelper->getEntityClass($entity);

            if ($entityClass === Order::class && $entity->getId()) {
                $paymentStatus = $this->paymentStatusManager
                    ->getPaymentStatusForEntity(Order::class, $entity->getId())
                    ->getPaymentStatus();

                if ($paymentStatus === PaymentStatusProvider::FULL) {
                    $orders[] = $entity;
                }

                continue;
            }

            if ($entityClass === PaymentStatus::class) {
                $paymentStatuses[] = $entity;
            }
        }

        if (count($orders) > 0) {
            $this->handleOrders($orders);
        }

        if (count($paymentStatuses) > 0) {
            $this->handlePaymentStatuses($paymentStatuses);
        }
    }

    /**
     * @param Order[] $orders
     */
    protected function handleOrders($orders)
    {
        /** @var Order $entity */
        foreach ($orders as $entity) {
            if (!$entity->getId() || $this->uow->isScheduledForDelete($entity)) {
                $this->scheduleUpdate($entity->getCustomer());
            } elseif ($this->uow->isScheduledForUpdate($entity)) {
                // handle update
                $changeSet = $this->uow->getEntityChangeSet($entity);

                if ($this->isChangeSetValuable($changeSet)) {
                    if (!empty($changeSet['customer'])
                        && reset($changeSet['customer']) instanceof Customer
                    ) {
                        // handle change of customer
                        $this->scheduleUpdate(reset($changeSet['customer']));
                    }

                    if (isset($changeSet['subtotalValue'])) {
                        $this->scheduleUpdate($entity->getCustomer());
                    }
                }
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->isInProgress || empty($this->queued)) {
            return;
        }

        $this->initializeFromEventArgs($args);
        $flushRequired = false;
        foreach ($this->queued as $customer) {
            if (!$customer->getId()) {
                // skip update for just removed customers
                continue;
            }
            $newLifetimeValue = $this->lifetimeProcessor->calculateLifetimeValue($customer);
            if ($newLifetimeValue != $customer->getLifetime()) {
                $customer->setLifetime($newLifetimeValue);
                $flushRequired = true;
            }
        }

        if ($flushRequired) {
            $this->isInProgress = true;
            $this->em->flush($this->queued);

            $this->isInProgress = false;
        }
        $this->queued = [];
    }

    /**
     * @param PaymentStatus[] $paymentStatuses
     */
    protected function handlePaymentStatuses($paymentStatuses)
    {
        /** @var PaymentStatus $paymentStatus */
        foreach ($paymentStatuses as $paymentStatus) {
            if ($paymentStatus->getEntityClass() === 'Oro\\Bundle\\OrderBundle\\Entity\\Order' &&
                PaymentStatusProvider::FULL === $paymentStatus->getPaymentStatus()
            ) {
                $order = $this->em->getRepository($paymentStatus->getEntityClass())
                    ->find($paymentStatus->getEntityIdentifier());
                if ($order) {
                    $this->scheduleUpdate($order->getCustomer());
                }
            }
        }
    }

    protected function scheduleUpdate(Customer $customer = null)
    {
        if ($customer == null || $this->uow->isScheduledForDelete($customer)) {
            return;
        }

        $this->queued[$customer->getId()] = $customer;
    }

    /**
     * @param array $changeSet
     *
     * @return bool
     */
    protected function isChangeSetValuable(array $changeSet)
    {
        $fieldsUpdated = array_intersect(['customer', 'subtotalValue'], array_keys($changeSet));

        return (bool)$fieldsUpdated;
    }

    /**
     * @param PostFlushEventArgs|OnFlushEventArgs $args
     */
    protected function initializeFromEventArgs($args)
    {
        $this->em  = $args->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();
    }
}
