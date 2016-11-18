<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\Strategy\AssignEachStrategy;
use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;

class AssignEachStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var AccountBuilder| */
    protected $accountBuilder;

    /** @var AssignEachStrategy */
    protected $ssignEachStrategy;

    protected function setUp()
    {
        $this->accountBuilder = new AccountBuilder();

        $this->ssignEachStrategy = new AssignEachStrategy(
            $this->accountBuilder
        );
    }

    public function testGetName()
    {
        self::assertEquals('each', $this->ssignEachStrategy->getName());
    }

    public function testProcessCustomerWithoutAccount()
    {
        $user = new User();
        $organization = new Organization();

        $customer = $this->initCustomer('Test Customer', $user, $organization);

        $expectedAccount = $this->initAccount('Test Customer', $user, $organization);
        $expectedCustomer = $this->initCustomer('Test Customer', $user, $organization);
        $expectedCustomer->setAccount($expectedAccount);

        self::assertEquals([
            $expectedAccount,
            $expectedCustomer
        ], $this->ssignEachStrategy->process($customer));
    }

    public function testProcessCustomerWithAccount()
    {
        $user = new User();
        $organization = new Organization();

        $customer = $this->initCustomer('Test Customer', $user, $organization);
        $account = $this->initAccount('Test Account', $user, $organization);
        $customer->setAccount($account);

        $expectedAccount = $this->initAccount('Test Account', $user, $organization);
        $expectedCustomer = $this->initCustomer('Test Customer', $user, $organization);
        $expectedCustomer->setAccount($expectedAccount);

        self::assertEquals([
            $expectedCustomer
        ], $this->ssignEachStrategy->process($customer));
    }

    public function testProcessCustomerWithAccountAndParent()
    {
        $user = new User();
        $organization = new Organization();

        $customer = $this->initCustomer('Test Customer', $user, $organization);
        $account = $this->initAccount('Test Account', $user, $organization);
        $customer->setAccount($account);

        $parentAccount = $this->initCustomer('Parent Customer', $user, $organization);
        $customer->setParent($parentAccount);

        $expectedAccount = $this->initAccount('Test Customer', $user, $organization);
        $expectedCustomer = $this->initCustomer('Test Customer', $user, $organization);
        $expectedCustomer->setAccount($expectedAccount);

        $expectedParentAccount = $this->initCustomer('Parent Customer', $user, $organization);
        $expectedCustomer->setParent($expectedParentAccount);

        self::assertEquals([
            $expectedAccount,
            $expectedCustomer
        ], $this->ssignEachStrategy->process($customer));
    }

    protected function initAccount($name, $owner, $organization)
    {
        $account = new Account();
        $account->setOwner($owner);
        $account->setName($name);
        $account->setOrganization($organization);

        return $account;
    }

    protected function initCustomer($name, $owner, $organization)
    {
        $customer = new Customer();
        $customer->setOwner($owner);
        $customer->setName($name);
        $customer->setOrganization($organization);

        return $customer;
    }
}
