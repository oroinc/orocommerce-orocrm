<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class CreateDefaultAccountFixture extends AbstractFixture
{
    const DEFAULT_ACCOUNT_REF = 'default_account';

    /** @var Organization */
    protected $organization;

    /**
     * @return Account
     */
    protected function createAccount()
    {
        $account = new Account();
        $account->setName('Default account');
        $account->setOrganization($this->organization);

        return $account;
    }

    public function load(ObjectManager $manager)
    {
        $this->organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();

        $account = $this->createAccount();

        $manager->persist($account);
        $manager->flush();

        $this->setReference(self::DEFAULT_ACCOUNT_REF, $account);
    }
}
