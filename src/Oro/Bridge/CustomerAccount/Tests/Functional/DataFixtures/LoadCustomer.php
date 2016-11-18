<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Tests\Functional\DataFixtures\LoadUserData;

class LoadCustomer extends AbstractFixture implements DependentFixtureInterface
{
    const CUSTOMER_1 = 'simple_customer';

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
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
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
        $account = $this->getReference($orderData['account']);

        $customer = new Customer();
        $customer->setName($name);
        $customer->setOwner($user);
        $customer->setAccount($account);

        $manager->persist($customer);
        $this->addReference($name, $customer);

        return $customer;
    }
}
