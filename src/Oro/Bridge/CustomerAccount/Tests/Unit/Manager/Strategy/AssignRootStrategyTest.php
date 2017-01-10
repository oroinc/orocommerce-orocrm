<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bridge\CustomerAccount\Manager\Strategy\AssignRootStrategy;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\CustomerAssociation;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

class AssignRootStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var AssignRootStrategy */
    protected $strategy;

    /** @var AccountCustomerManager */
    protected $manager;

    /** @var AccountBuilder */
    protected $builder;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->manager = $this->getAccountCustomerManager();
        $this->builder = $this->createAccountBuilderMock();
        $this->strategy = new AssignRootStrategy(
            $this->builder,
            $this->manager,
            $this->getLifetimeProcessor()
        );
    }

    public function testCorrectName()
    {
        $name = $this->strategy->getName();
        $this->assertEquals(AssignRootStrategy::NAME, $name);
    }

    public function testCreateNewEntitiesIfNoParentCustomer()
    {
        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget(new Account());

        $this->manager->method('getAccountCustomerByTarget')->willReturn($customerAssociation);

        $entity = new Customer();
        $results = $this->strategy->process($entity);

        $this->assertCount(2, $results);
    }

    public function testGetParentValueIfIsParentCustomerWithAccount()
    {

        $parentAccount = new Account();
        $parentAccount->setId(1);

        $account = new Account();
        $account->setId(2);

        $entity = new Customer();
        $parent = new Customer();

        $parent->setAccount($parentAccount);
        $entity->setParent($parent);

        $rootCustomerAssociation = new CustomerAssociation();
        $rootCustomerAssociation->setTarget($parentAccount);

        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account);

        $this->manager
            ->method('getAccountCustomerByTarget')
            ->willReturnOnConsecutiveCalls($rootCustomerAssociation, $customerAssociation);

        $results = $this->strategy->process($entity);

        $this->assertCount(2, $results);
        $this->assertNull($results[0]->getAccount());
        $this->assertEquals($account, $results[0]->getPreviousAccount());
        $this->assertEquals($parentAccount, $results[1]->getAccount());
    }

    public function testCreateNewEntityIfIsParentCustomerWithoutAccount()
    {
        $account = new Account();
        $account->setId(2);

        $entity = new Customer();
        $parent = new Customer();
        $entity->setParent($parent);

        $rootCustomerAssociation = new CustomerAssociation();

        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account);

        $this->manager
            ->expects($this->exactly(2))
            ->method('getAccountCustomerByTarget')
            ->willReturnOnConsecutiveCalls($rootCustomerAssociation, $customerAssociation);

        $this->builder->expects($this->once())
            ->method('build')
            ->willReturn($account);

        $results = $this->strategy->process($entity);

        $this->assertCount(3, $results);
        $this->assertEquals($account, $results[1]->getPreviousAccount());
        $this->assertNull($results[1]->getAccount());
        $this->assertEquals($account, $results[2]->getAccount());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AccountBuilder
     */
    private function createAccountBuilderMock()
    {
        return $this->createMock(AccountBuilder::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AccountCustomerManager
     */
    private function getAccountCustomerManager()
    {
        $manager = $this->getMockBuilder(AccountCustomerManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $manager;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LifetimeProcessor
     */
    private function getLifetimeProcessor()
    {
        $processor = $this->getMockBuilder(LifetimeProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $processor;
    }
}
