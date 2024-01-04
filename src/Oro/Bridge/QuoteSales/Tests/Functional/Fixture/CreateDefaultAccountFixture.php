<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class CreateDefaultAccountFixture extends AbstractFixture
{
    public const DEFAULT_ACCOUNT_REF = 'default_account';

    public function load(ObjectManager $manager)
    {
        $account = new Account();
        $account->setName('Default account');
        $account->setOrganization($manager->getRepository(Organization::class)->getFirst());
        $manager->persist($account);
        $manager->flush();
        $this->setReference(self::DEFAULT_ACCOUNT_REF, $account);
    }
}
