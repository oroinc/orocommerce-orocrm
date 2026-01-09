<?php

namespace Oro\Bridge\CustomerAccount\Manager;

use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;

/**
 * Creates new account entities from customer data.
 *
 * Builds a new Account instance populated with data from a given Customer entity,
 * including name, organization, and owner information. Used during customer account
 * assignment workflows to establish the initial account for a customer.
 */
class AccountBuilder
{
    /**
     * Build new account for customer
     *
     * @param Customer $entity
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
