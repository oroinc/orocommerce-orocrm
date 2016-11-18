<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\AccountBundle\Entity\Account;

class CustomerCreateListener
{
    /** @var UnitOfWork */
    protected $uow;

    /** @var EntityManager */
    protected $em;

    /** @var Customer[] */
    protected $queued = [];

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $this->initializeFromEventArgs($args);

        foreach ($this->uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Customer) {
                $this->queued[] = $entity;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $this->initializeFromEventArgs($args);
        if ($this->queued) {
            foreach ($this->queued as $entity) {
                if (!$entity->getAccount()) {
                    $account = new Account();
                    $account->setName($entity->getName());
                    $account->setOrganization($entity->getOrganization());
                    $account->setOwner($entity->getOwner());
                    $this->em->persist($account);
                    $entity->setAccount($account);
                    $this->em->persist($entity);
                }
            }
            $this->queued = [];
            $this->em->flush();
        }
    }

    /**
     * @param OnFlushEventArgs|PostFlushEventArgs $args
     */
    protected function initializeFromEventArgs($args)
    {
        $this->em  = $args->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();
    }
}
