<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

class AssignRootStrategy extends AssignStrategyAbstract
{
    const NAME = 'root';

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
        $account = null;
        $rootCustomer = $this->getRootCustomer($entity);
        if ($rootCustomer->getAccount()) {
            $account = $rootCustomer->getAccount();
        }
        if (!$account) {
            $account = $this->builder->build($entity);
            $objects[] = $account;
        }
        $entity->setPreviousAccount($entity->getAccount());
        $entity->setAccount($account);
        $objects[] = $entity;

        return $objects;
    }
}
