<?php

namespace Oro\Bridge\CustomerAccount\Provider\Customer;

use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\SalesBundle\Provider\Customer\AccountCreation\AccountProviderInterface;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Entity\UserManager;

class AccountProvider implements AccountProviderInterface
{
    /** @var ConfigManager */
    protected $configManager;

    /** @var UserManager */
    protected $userManager;

    /**
     * @param ConfigManager $configManager
     * @param UserManager   $userManager
     */
    public function __construct(ConfigManager $configManager, UserManager $userManager)
    {
        $this->configManager = $configManager;
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccount($targetCustomer)
    {
        if (!$targetCustomer instanceof Customer) {
            return null;
        }

        $account = new Account();
        $account->setName($targetCustomer->getName());
        $owner = $targetCustomer->getOwner();
        if ($owner === null) {
            $userId = $this->configManager->get('oro_customer.default_account_owner');

            /** @var User $user */
            $owner = $this->userManager->getRepository()->find($userId);
        }

        if ($owner) {
            $account->setOwner($owner);
        }

        return $account;
    }
}
