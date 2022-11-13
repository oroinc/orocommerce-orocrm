<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bridge\CustomerAccount\Manager\Strategy\AssignEachStrategy;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\SalesBundle\Tests\Unit\Fixture\CustomerStub as CustomerAssociation;
use Oro\Bundle\UserBundle\Entity\User;

class AssignEachStrategyTest extends \PHPUnit\Framework\TestCase
{
    /** @var AccountCustomerManager|\PHPUnit\Framework\MockObject\MockObject */
    private $manager;

    /** @var AssignEachStrategy */
    private $assignEachStrategy;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(AccountCustomerManager::class);

        $this->assignEachStrategy = new AssignEachStrategy(
            new AccountBuilder(),
            $this->manager,
            $this->createMock(LifetimeProcessor::class)
        );
    }

    public function testGetName()
    {
        self::assertEquals('each', $this->assignEachStrategy->getName());
    }

    public function testProcessCustomerWithoutAccount()
    {
        $user = new User();
        $organization = new Organization();

        $customer = $this->initCustomer('Test Customer', $user, $organization);
        $expectedAccount = $this->initAccount('Test Customer', $user, $organization);
        $customerAssociation = new CustomerAssociation();

        $this->manager->expects(self::any())
            ->method('getAccountCustomerByTarget')
            ->willReturn($customerAssociation);

        self::assertEquals(
            [
                $expectedAccount,
                $customer,
                $customerAssociation,
            ],
            $this->assignEachStrategy->process($customer)
        );

        self::assertEquals($customer, $customerAssociation->getTarget());
    }

    public function testProcessCustomerWithAccount()
    {
        $user = new User();
        $organization = new Organization();

        $customer = $this->initCustomer('Test Customer', $user, $organization);
        $account = $this->initAccount('Test Account', $user, $organization);

        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account);

        $this->manager->expects(self::any())
            ->method('getAccountCustomerByTarget')
            ->willReturn($customerAssociation);

        self::assertEquals(
            [
                $customer,
                $customerAssociation,
            ],
            $this->assignEachStrategy->process($customer)
        );

        self::assertEquals($customer, $customerAssociation->getTarget());
    }

    public function testProcessCustomerWithAccountAndParent()
    {
        $user = new User();
        $organization = new Organization();

        $customer = $this->initCustomer('Test Customer', $user, $organization);
        $account = $this->initAccount('Test Account', $user, $organization);

        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account);

        $parentCustomer = $this->initCustomer('Parent Customer', $user, $organization);
        $parentAccount = $this->initAccount('Parent Account', $user, $organization);
        $customer->setParent($parentCustomer);

        $rootCustomerAssociation = new CustomerAssociation();
        $rootCustomerAssociation->setTarget($parentAccount);

        $this->manager->expects(self::any())
            ->method('getAccountCustomerByTarget')
            ->willReturnOnConsecutiveCalls($customerAssociation, $rootCustomerAssociation);

        self::assertEquals(
            [
                $customer,
                $customerAssociation,
            ],
            $this->assignEachStrategy->process($customer)
        );

        self::assertEquals($customer, $customerAssociation->getTarget());
    }

    private function initAccount(string $name, User $owner, Organization $organization): Account
    {
        $account = new Account();
        $account->setOwner($owner);
        $account->setName($name);
        $account->setOrganization($organization);

        return $account;
    }

    private function initCustomer(string $name, User $owner, Organization $organization): Customer
    {
        $customer = new Customer();
        $customer->setOwner($owner);
        $customer->setName($name);
        $customer->setOrganization($organization);

        return $customer;
    }
}
