<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

/**
 * Assigns customers to the account of their root customer.
 *
 * Implements a customer-to-account assignment strategy where child customers are
 * assigned to the same account as their root customer. If the root customer has no
 * account, a new one is created. This strategy maintains hierarchical customer
 * relationships and ensures all customers in a hierarchy share the same account.
 */
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
