<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\PaymentBundle\Entity\PaymentStatus;
use Oro\Bundle\PaymentBundle\Manager\PaymentStatusManager;
use Oro\Bundle\PaymentBundle\Provider\PaymentStatusProvider;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Calculates and keeps actual customer lifetime value.
 */
class CustomerLifetimeListener implements ServiceSubscriberInterface
{
    private DoctrineHelper $doctrineHelper;
    private ContainerInterface $container;
    private ?LifetimeProcessor $lifetimeProcessor = null;
    private ?PaymentStatusManager $paymentStatusManager = null;
    /** @var Customer[] */
    private array $queued = [];
    private bool $isInProgress = false;

    public function __construct(DoctrineHelper $doctrineHelper, ContainerInterface $container)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedServices()
    {
        return [
            'oro_customer_account.manager.lifetime_processor' => LifetimeProcessor::class,
            'oro_payment.manager.payment_status' => PaymentStatusManager::class
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $orders = [];
        $paymentStatuses = [];
        $entities = $this->getChangedEntities($uow);
        foreach ($entities as $entity) {
            $entityClass = $this->doctrineHelper->getEntityClass($entity);

            if ($entityClass === Order::class && $entity->getId()) {
                $paymentStatus = $this->getPaymentStatusManager()
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
            $this->handleOrders($orders, $uow);
        }

        if (count($paymentStatuses) > 0) {
            $this->handlePaymentStatuses($paymentStatuses, $uow, $em);
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->isInProgress || empty($this->queued)) {
            return;
        }

        $em = $args->getEntityManager();

        $flushRequired = false;
        foreach ($this->queued as $customer) {
            if (!$customer->getId()) {
                // skip update for just removed customers
                continue;
            }

            $newLifetimeValue = $this->getLifetimeProcessor()->calculateLifetimeValue($customer);
            /** @noinspection TypeUnsafeComparisonInspection */
            if ($newLifetimeValue != $customer->getLifetime()) {
                $customer->setLifetime($newLifetimeValue);
                $flushRequired = true;
            }
        }

        if ($flushRequired) {
            $this->isInProgress = true;
            try {
                $em->flush($this->queued);
            } finally {
                $this->isInProgress = false;
            }
        }
        $this->queued = [];
    }

    private function getChangedEntities(UnitOfWork $uow): \Generator
    {
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            yield $entity;
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            yield $entity;
        }
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            yield $entity;
        }
    }

    /**
     * @param Order[]    $orders
     * @param UnitOfWork $uow
     */
    private function handleOrders(array $orders, UnitOfWork $uow): void
    {
        /** @var Order $entity */
        foreach ($orders as $entity) {
            if (!$entity->getId() || $uow->isScheduledForDelete($entity)) {
                $this->scheduleUpdate($entity->getCustomer(), $uow);
            } elseif ($uow->isScheduledForUpdate($entity)) {
                // handle update
                $changeSet = $uow->getEntityChangeSet($entity);

                if ($this->isChangeSetValuable($changeSet)) {
                    if (!empty($changeSet['customer'])
                        && reset($changeSet['customer']) instanceof Customer
                    ) {
                        // handle change of customer
                        $this->scheduleUpdate(reset($changeSet['customer']), $uow);
                    }

                    if (isset($changeSet['subtotalValue'])) {
                        $this->scheduleUpdate($entity->getCustomer(), $uow);
                    }
                }
            }
        }
    }

    /**
     * @param PaymentStatus[]        $paymentStatuses
     * @param UnitOfWork             $uow
     * @param EntityManagerInterface $em
     */
    private function handlePaymentStatuses(array $paymentStatuses, UnitOfWork $uow, EntityManagerInterface $em): void
    {
        /** @var PaymentStatus $paymentStatus */
        foreach ($paymentStatuses as $paymentStatus) {
            if ($paymentStatus->getEntityClass() === Order::class
                && PaymentStatusProvider::FULL === $paymentStatus->getPaymentStatus()
            ) {
                $order = $em->getRepository($paymentStatus->getEntityClass())
                    ->find($paymentStatus->getEntityIdentifier());
                if ($order) {
                    $this->scheduleUpdate($order->getCustomer(), $uow);
                }
            }
        }
    }

    private function scheduleUpdate(?Customer $customer, UnitOfWork $uow): void
    {
        if (null === $customer || $uow->isScheduledForDelete($customer)) {
            return;
        }

        $this->queued[$customer->getId()] = $customer;
    }

    private function isChangeSetValuable(array $changeSet): bool
    {
        $fieldsUpdated = array_intersect(['customer', 'subtotalValue'], array_keys($changeSet));

        return (bool)$fieldsUpdated;
    }

    private function getLifetimeProcessor(): LifetimeProcessor
    {
        if (null === $this->lifetimeProcessor) {
            $this->lifetimeProcessor = $this->container->get('oro_customer_account.manager.lifetime_processor');
        }

        return $this->lifetimeProcessor;
    }

    private function getPaymentStatusManager(): PaymentStatusManager
    {
        if (null === $this->paymentStatusManager) {
            $this->paymentStatusManager = $this->container->get('oro_payment.manager.payment_status');
        }

        return $this->paymentStatusManager;
    }
}
