<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bundle\CurrencyBundle\Converter\RateConverterInterface;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\PaymentBundle\Entity\PaymentStatus;
use Oro\Bundle\PaymentBundle\Provider\PaymentStatusProvider;
use Oro\Component\DependencyInjection\ServiceLink;

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

    /** @var ServiceLink */
    protected $paymentStatusProviderLink;

    /** @var PaymentStatusProvider */
    protected $paymentStatusProvider;

    /**
     * @param ServiceLink $rateConverterLink
     * @param LifetimeProcessor $lifetimeProcessor
     * @param ServiceLink $paymentStatusProviderLink
     */
    public function __construct(
        ServiceLink $rateConverterLink,
        LifetimeProcessor $lifetimeProcessor,
        ServiceLink $paymentStatusProviderLink
    ) {
        $this->rateConverter = $rateConverterLink->getService();
        $this->lifetimeProcessor = $lifetimeProcessor;
        $this->paymentStatusProviderLink = $paymentStatusProviderLink;
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $this->initializeFromEventArgs($args);

        $entities = array_merge(
            $this->uow->getScheduledEntityInsertions(),
            $this->uow->getScheduledEntityDeletions(),
            $this->uow->getScheduledEntityUpdates()
        );

        /** @var Order[] $entitiesOrder */
        $orders = array_filter(
            $entities,
            function ($entity) {
                $paymentStatus = null;
                if ('Oro\\Bundle\\OrderBundle\\Entity\\Order' === ClassUtils::getClass($entity)) {
                    $paymentStatus = $this->getPaymentStatusProvider()->getPaymentStatus($entity);
                }

                return PaymentStatusProvider::FULL === $paymentStatus;
            }
        );

        /** @var $paymentStatuses[] PaymentStatus */
        $paymentStatuses = array_filter(
            $entities,
            function ($entity) {
                return 'Oro\\Bundle\\PaymentBundle\\Entity\\PaymentStatus' === ClassUtils::getClass($entity);
            }
        );

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

    /**
     * @param PostFlushEventArgs $args
     */
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

    /**
     * @param Customer|null $customer
     */
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

    /**
     * @return object|PaymentStatusProvider
     */
    protected function getPaymentStatusProvider()
    {
        if (!$this->paymentStatusProvider) {
            $this->paymentStatusProvider = $this->paymentStatusProviderLink->getService();
        }

        return $this->paymentStatusProvider;
    }
}
