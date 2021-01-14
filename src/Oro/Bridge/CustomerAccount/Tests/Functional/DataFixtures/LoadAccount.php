<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Tests\Functional\DataFixtures\LoadUserData;

class LoadAccount extends AbstractFixture implements DependentFixtureInterface
{
    const ACCOUNT_1 = 'simple_account';

    /**
     * @var array
     */
    protected $accounts = [
        self::ACCOUNT_1 => [
            'user' => 'simple_user',
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            LoadUserData::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->accounts as $name => $order) {
            $this->createAccount($manager, $name, $order);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string $name
     * @param array $orderData
     *
     * @return Account
     */
    protected function createAccount(ObjectManager $manager, $name, array $orderData)
    {
        /** @var User $user */
        $user = $this->getReference($orderData['user']);

        $account = new Account();
        $account->setName($name);
        $account->setOwner($user);

        $manager->persist($account);
        $this->addReference($name, $account);

        return $account;
    }
}
