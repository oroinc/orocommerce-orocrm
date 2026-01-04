<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

class AssignRootStrategy extends AssignStrategyAbstract
{
    public const NAME = 'root';

    #[\Override]
    public function getName()
    {
        return self::NAME;
    }

    #[\Override]
    public function process($entity)
    {
        $objects = [];
        $account = null;
        $rootCustomer = $this->getRootCustomer($entity);
        $rootCustomerAssociation = $this->accountCustomerManager->getAccountCustomerByTarget($rootCustomer);
        if ($rootCustomerAssociation->getAccount()) {
            $account = $rootCustomerAssociation->getAccount();
        }
        if (!$account) {
            $account = $this->builder->build($entity);
            $objects[] = $account;
        }

        $customerAssociation = $this->accountCustomerManager->getAccountCustomerByTarget($entity);
        $entity->setPreviousAccount($customerAssociation->getAccount());
        $customerAssociation->setTarget($account, $entity);

        $this->recalculateLifeTimeValue($entity);
        $objects[] = $entity;
        $objects[] = $customerAssociation;

        return $objects;
    }
}
