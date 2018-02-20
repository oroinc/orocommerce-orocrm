<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\SalesBundle\Entity\Customer as CustomerAssociation;

class AssignEachStrategy extends AssignStrategyAbstract
{
    const NAME = 'each';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function process($entity)
    {
        $objects = [];

        $customerAssociation = $this->accountCustomerManager->getAccountCustomerByTarget($entity);

        $account = $this->getPreviousAccount($entity, $customerAssociation);
        if (!$account) {
            $account = $this->builder->build($entity);
            $objects[] = $account;
        }
        $this->recalculateLifeTimeValue($entity);
        $customerAssociation->setTarget($account, $entity);
        $objects[] = $entity;
        $objects[] = $customerAssociation;

        return $objects;
    }

    /**
     * Try to get previous account to child customers
     *
     * @param Customer            $customer
     * @param CustomerAssociation $customerAssociation
     *
     * @return Account|null
     */
    protected function getPreviousAccount(Customer $customer, CustomerAssociation $customerAssociation)
    {
        $account = null;
        $currentAccount = $customerAssociation->getAccount();

        if ($customer->getParent() !== null) {
            $rootCustomer = $this->getRootCustomer($customer);
            $rootCustomerAssociation = $this->accountCustomerManager->getAccountCustomerByTarget($rootCustomer);
            $rootAccount = $rootCustomerAssociation->getAccount();

            $previousAccount = $customer->getPreviousAccount();
            if ($previousAccount && $previousAccount !== $currentAccount && $previousAccount !== $rootAccount) {
                $account = $previousAccount;
            }

            if ($account === null && $currentAccount && $rootAccount && $rootAccount !== $currentAccount) {
                $account = $currentAccount;
            }
        } else {
            $account = $currentAccount;
        }

        return $account;
    }
}
