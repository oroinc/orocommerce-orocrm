<?php

namespace Oro\Bridge\CustomerAccount\Provider\Customer;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Provider\Customer\AccountCreation\AccountProviderInterface;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Creates new Account entity to be associated with Customer entity.
 */
class AccountProvider implements AccountProviderInterface
{
    /** @var ConfigManager */
    private $configManager;

    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(
        ConfigManager $configManager,
        ManagerRegistry $doctrine
    ) {
        $this->configManager = $configManager;
        $this->doctrine = $doctrine;
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
        $organization = $targetCustomer->getOrganization();
        $owner = $targetCustomer->getOwner();
        if (null === $owner) {
            $userId = $this->configManager->get('oro_customer.default_customer_owner');
            $owner = $this->doctrine->getManagerForClass(User::class)->find(User::class, $userId);
        }
        if (null !== $owner) {
            $account->setOwner($owner);
            if (!$organization) {
                $organization = $owner->getOrganization();
            }
        }
        $account->setOrganization($organization);

        if (!$targetCustomer->getDataChannel()) {
            $channels = $this->doctrine->getManagerForClass(Channel::class)
                ->getRepository(Channel::class)
                ->findBy(['channelType' => 'commerce']);
            if (count($channels) > 0) {
                $targetCustomer->setDataChannel(reset($channels));
            }
        }

        return $account;
    }
}
