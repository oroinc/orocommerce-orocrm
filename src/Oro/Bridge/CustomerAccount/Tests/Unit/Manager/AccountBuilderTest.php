<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Manager;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;

class AccountBuilderTest extends \PHPUnit\Framework\TestCase
{
    /** @var AccountBuilder */
    protected $accountBuilder;

    protected function setUp(): void
    {
        $this->accountBuilder = new AccountBuilder();
    }

    public function testBuildEntityCustomer()
    {
        $owner = new User();
        $entity = new Customer();
        $entity->setName('Test Customer');
        $entity->setOwner($owner);
        $entity->setOrganization(new Organization());

        $expectedAccount = new Account();
        $expectedAccount->setName('Test Customer');
        $expectedAccount->setOwner($owner);
        $expectedAccount->setOrganization(new Organization());

        $account = $this->accountBuilder->build($entity);

        self::assertEquals($expectedAccount, $account);
    }
}
