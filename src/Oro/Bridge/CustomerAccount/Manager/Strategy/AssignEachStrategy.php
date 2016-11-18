<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

use Oro\Bundle\CustomerBundle\Entity\Account as Customer;

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
        $account = $this->getPreviousAccount($entity);
        if (null === $account) {
            $account = $this->builder->build($entity);
            $objects[] = $account;
        }
        $entity->setAccount($account);
        $objects[] = $entity;

        return $objects;
    }

    /**
     * Try to get previous account to child customers
     *
     * @param Customer $customer
     *
     * @return mixed
     */
    protected function getPreviousAccount($customer)
    {
        $account = null;
        $currentAccount = $customer->getAccount();
        if ($customer->getParent() !== null) {
            $rootCustomer = $this->getRootCustomer($customer);
            $previousAccount = $customer->getPreviousAccount();
            $rootAccount = $rootCustomer->getAccount();
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
