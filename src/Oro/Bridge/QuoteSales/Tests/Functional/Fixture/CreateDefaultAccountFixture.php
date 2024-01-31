<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadOrganization;

class CreateDefaultAccountFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const DEFAULT_ACCOUNT_REF = 'default_account';

    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [LoadOrganization::class];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setName('Default account');
        $account->setOrganization($this->getReference(LoadOrganization::ORGANIZATION));
        $manager->persist($account);
        $manager->flush();
        $this->setReference(self::DEFAULT_ACCOUNT_REF, $account);
    }
}
