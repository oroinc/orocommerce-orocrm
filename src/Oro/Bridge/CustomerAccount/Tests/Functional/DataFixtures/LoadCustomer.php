<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Customer as CustomerAssociation;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadOrganization;
use Oro\Bundle\UserBundle\Tests\Functional\DataFixtures\LoadUserData;
use Oro\Component\DependencyInjection\ContainerAwareInterface;
use Oro\Component\DependencyInjection\ContainerAwareTrait;

class LoadCustomer extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public const CUSTOMER_1 = 'simple_customer';
    public const CHANNEL_TYPE = 'commerce';
    public const CHANNEL_NAME = 'Commerce Channel';

    private array $customers = [
        self::CUSTOMER_1 => [
            'user' => 'simple_user',
            'account' => 'simple_account'
        ]
    ];

    #[\Override]
    public function getDependencies(): array
    {
        return [LoadAccount::class, LoadUserData::class, LoadOrganization::class];
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $this->createChannel($manager);
        foreach ($this->customers as $name => $customer) {
            $this->createCustomer($manager, $name, $customer);
        }
        $manager->flush();
    }

    private function createCustomer(ObjectManager $manager, string $name, array $orderData): void
    {
        /** @var Account $account */
        $account = $this->getReference($orderData['account']);

        $customer = new Customer();
        $customer->setName($name);
        $customer->setOwner($this->getReference($orderData['user']));
        $customer->setDataChannel($this->getReference('commerce_channel'));

        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account, $customer);

        $manager->persist($customer);
        $manager->persist($customerAssociation);

        $this->addReference($name, $customer);
    }

    private function createChannel(ObjectManager $manager): void
    {
        $channel = $this->container->get('oro_channel.builder.factory')
            ->createBuilder()
            ->setName(self::CHANNEL_NAME)
            ->setChannelType(self::CHANNEL_TYPE)
            ->setStatus(Channel::STATUS_ACTIVE)
            ->setOwner($this->getReference(LoadOrganization::ORGANIZATION))
            ->setEntities()
            ->getChannel();

        $manager->persist($channel);
        $manager->flush();

        $this->setReference('commerce_channel', $channel);
    }
}
