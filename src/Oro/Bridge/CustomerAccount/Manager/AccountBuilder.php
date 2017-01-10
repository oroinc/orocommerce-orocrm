<?php

namespace Oro\Bridge\CustomerAccount\Manager;

use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;

class AccountBuilder
{
    /**
     * Build new account for customer
     *
     * @param $entity
     *
     * @return Account
     */
    public function build(Customer $entity)
    {
        $account = new Account();
        $account->setName($entity->getName());
        $account->setOrganization($entity->getOrganization());
        $account->setOwner($entity->getOwner());

        return $account;
    }
}
