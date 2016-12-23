<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bundle\CurrencyBundle\Converter\RateConverterInterface;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\OrderBundle\Entity\Order;
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

    /**
     * @param ServiceLink $rateConverterLink
     * @param LifetimeProcessor $lifetimeProcessor
     */
    public function __construct(
        ServiceLink $rateConverterLink,
        LifetimeProcessor $lifetimeProcessor
    ) {
        $this->rateConverter = $rateConverterLink->getService();
        $this->lifetimeProcessor = $lifetimeProcessor;
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

        /** @var Order[] $entities */
        $entities = array_filter(
            $entities,
            function ($entity) {
                return 'Oro\\Bundle\\OrderBundle\\Entity\\Order' === ClassUtils::getClass($entity);
            }
        );

        foreach ($entities as $entity) {
            if (!$entity->getId()) {
                // handle creation, just add to prev lifetime value and recalculate change set
                $customer = $entity->getAccount();
                $subtotalValue = $this->rateConverter->getBaseCurrencyAmount($entity->getSubtotalObject());
                $customer->setLifetime($customer->getLifetime() + $subtotalValue);
                $this->scheduleUpdate($customer);
                $this->uow->computeChangeSet(
                    $this->em->getClassMetadata(ClassUtils::getClass($customer)),
                    $customer
                );
            } elseif ($this->uow->isScheduledForDelete($entity)) {
                $this->scheduleUpdate($entity->getAccount());
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
                        $this->scheduleUpdate($entity->getAccount());
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
     * @param Customer $customer
     */
    protected function scheduleUpdate(Customer $customer)
    {
        if ($this->uow->isScheduledForDelete($customer)) {
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
        $fieldsUpdated = array_intersect(['account', 'subtotalValue'], array_keys($changeSet));

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
