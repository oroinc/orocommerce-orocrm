<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Builder\BuilderFactory;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SalesBundle\Entity\Customer as CustomerAssociation;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Tests\Functional\DataFixtures\LoadUserData;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCustomer extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    const CUSTOMER_1 = 'simple_customer';
    const CHANNEL_TYPE  = 'commerce';
    const CHANNEL_NAME  = 'Commerce Channel';

    /** @var ObjectManager */
    protected $em;

    /** @var BuilderFactory */
    protected $factory;

    /** @var Channel */
    protected $channel;

    /** @var User */
    protected $user;

    /** @var Organization */
    protected $organization;

    /**
     * @var array
     */
    protected $customers = [
        self::CUSTOMER_1 => [
            'user' => 'simple_user',
            'account' => 'simple_account'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            LoadAccount::class,
            LoadUserData::class
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->factory = $container->get('oro_channel.builder.factory');
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->em = $manager;
        $this->organization = $manager->getRepository(Organization::class)->getFirst();
        $this->createChannel();

        foreach ($this->customers as $name => $customer) {
            $this->createCustomer($manager, $name, $customer);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string $name
     * @param array $orderData
     *
     * @return Customer
     */
    protected function createCustomer(ObjectManager $manager, $name, array $orderData)
    {
        /** @var User $user */
        $user = $this->getReference($orderData['user']);

        /** @var Account $account */
        $account = $this->getReference($orderData['account']);

        $customer = new Customer();
        $customer->setName($name);
        $customer->setOwner($user);
        $customer->setDataChannel($this->getReference('commerce_channel'));

        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account, $customer);

        $manager->persist($customer);
        $manager->persist($customerAssociation);

        $this->addReference($name, $customer);

        return $customer;
    }

    /**
     * @return Channel
     */
    protected function createChannel()
    {
        $channel = $this
            ->factory
            ->createBuilder()
            ->setName(self::CHANNEL_NAME)
            ->setChannelType(self::CHANNEL_TYPE)
            ->setStatus(Channel::STATUS_ACTIVE)
            ->setOwner($this->organization)
            ->setEntities()
            ->getChannel();

        $this->em->persist($channel);
        $this->em->flush();

        $this->setReference('commerce_channel', $channel);

        return $this;
    }
}
