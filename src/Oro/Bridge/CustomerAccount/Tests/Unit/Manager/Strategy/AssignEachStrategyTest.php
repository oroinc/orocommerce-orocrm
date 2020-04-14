<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bridge\CustomerAccount\Manager\Strategy\AssignEachStrategy;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\CustomerAssociation;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\UserBundle\Entity\User;

class AssignEachStrategyTest extends \PHPUnit\Framework\TestCase
{
    /** @var AccountBuilder */
    protected $accountBuilder;

    /** @var AssignEachStrategy */
    protected $assignEachStrategy;

    /** @var AccountCustomerManager */
    protected $manager;

    protected function setUp(): void
    {
        $this->accountBuilder = new AccountBuilder();
        $this->manager = $this->getAccountCustomerManager();

        $this->assignEachStrategy = new AssignEachStrategy(
            $this->accountBuilder,
            $this->manager,
            $this->getLifetimeProcessor()
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

        $this->manager->method('getAccountCustomerByTarget')->willReturn($customerAssociation);

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

        $this->manager->method('getAccountCustomerByTarget')->willReturn($customerAssociation);

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

        $this->manager->method('getAccountCustomerByTarget')
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

    /**
     * @param $name
     * @param $owner
     * @param $organization
     * @return Account
     */
    protected function initAccount($name, $owner, $organization)
    {
        $account = new Account();
        $account->setOwner($owner);
        $account->setName($name);
        $account->setOrganization($organization);

        return $account;
    }

    /**
     * @param $name
     * @param $owner
     * @param $organization
     * @return Customer
     */
    protected function initCustomer($name, $owner, $organization)
    {
        $customer = new Customer();
        $customer->setOwner($owner);
        $customer->setName($name);
        $customer->setOrganization($organization);

        return $customer;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|AccountCustomerManager
     */
    private function getAccountCustomerManager()
    {
        $manager = $this->getMockBuilder(AccountCustomerManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $manager;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|LifetimeProcessor
     */
    private function getLifetimeProcessor()
    {
        $processor = $this->getMockBuilder(LifetimeProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $processor;
    }
}
