<?php

namespace Oro\Bridge\CustomerAccount\Provider\Customer;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\SalesBundle\Provider\Customer\AccountCreation\AccountProviderInterface;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Creates new Account entity to be associated with Customer entity.
 */
class AccountProvider implements AccountProviderInterface
{
    private const CONFIG_KEY = 'oro_customer_account_bridge.customer_account_settings';

    public function __construct(
        private ConfigManager $configManager,
        private ManagerRegistry $doctrine,
        private AccountCustomerManager $customerManager
    ) {
    }

    #[\Override]
    public function getAccount($targetCustomer): ?Account
    {
        if (!$targetCustomer instanceof Customer) {
            return null;
        }

        return match ($this->configManager->get(self::CONFIG_KEY)) {
            'root' => $this->getRootAccount($targetCustomer),
            'each' => $this->getEachAccount($targetCustomer)
        };
    }

    private function getRootAccount(Customer $customer): ?Account
    {
        $rootCustomer = $this->getRootCustomer($customer);
        $association = $this->customerManager->getAccountCustomerByTarget($rootCustomer, false);

        return $association?->getAccount() ?? $this->getEachAccount($customer);
    }

    private function getEachAccount(Customer $customer): ?Account
    {
        $account = new Account();
        $account->setName($customer->getName());
        $owner = $customer->getOwner();
        if (null === $owner) {
            $userId = $this->configManager->get('oro_customer.default_customer_owner');
            $owner = $this->doctrine->getManagerForClass(User::class)->find(User::class, $userId);
        }

        $account->setOwner($owner);
        $account->setOrganization($customer->getOrganization() ?? $owner->getOrganization());
        if (!$customer->getDataChannel()) {
            $channel = $this->getDataChannel();
            if ($channel) {
                $customer->setDataChannel($channel);
            }
        }

        return $account;
    }

    private function getRootCustomer(Customer $customer): Customer
    {
        return $customer->getParent() ? $this->getRootCustomer($customer->getParent()) : $customer;
    }

    private function getDataChannel(): ?Channel
    {
        $repository = $this->doctrine->getManagerForClass(Channel::class)->getRepository(Channel::class);
        $channels = $repository->findBy(['channelType' => 'commerce']);

        return reset($channels);
    }
}
