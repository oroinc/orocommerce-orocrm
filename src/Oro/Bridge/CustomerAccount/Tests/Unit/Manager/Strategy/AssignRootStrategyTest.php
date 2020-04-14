<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bridge\CustomerAccount\Manager\Strategy\AssignRootStrategy;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\CustomerAssociation;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

class AssignRootStrategyTest extends \PHPUnit\Framework\TestCase
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
    protected function setUp(): void
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

        $this->manager->method('getAccountCustomerByTarget')->willReturn($customerAssociation);
        $this->builder->method('build')->willReturn(new Account());

        $entity = new Customer();
        $results = $this->strategy->process($entity);

        $this->assertCount(3, $results);
    }

    public function testGetParentValueIfIsParentCustomerWithAccount()
    {
        $parentAccount = new Account();

        $entity = new Customer();
        $parent = new Customer();

        $parent->setAccount($parentAccount);
        $entity->setParent($parent);

        $rootCustomerAssociation = new CustomerAssociation();
        $rootCustomerAssociation->setTarget($parentAccount);

        $customerAssociation = new CustomerAssociation();

        $this->manager
            ->method('getAccountCustomerByTarget')
            ->willReturnOnConsecutiveCalls($rootCustomerAssociation, $customerAssociation);

        $results = $this->strategy->process($entity);

        $this->assertCount(2, $results);
        $this->assertEquals($parentAccount, $results[1]->getAccount());
        $this->assertNull($entity->getPreviousAccount());
        $this->assertNull($results[0]->getAccount());
    }

    public function testCreateNewEntityIfIsParentCustomerWithoutAccount()
    {
        $entity = new Customer();
        $parent = new Customer();
        $entity->setParent($parent);

        $account = new Account();
        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account);
        $rootCustomerAssociation = new CustomerAssociation();

        $this->manager
            ->expects($this->exactly(2))
            ->method('getAccountCustomerByTarget')
            ->willReturnOnConsecutiveCalls($rootCustomerAssociation, $customerAssociation);

        $this->builder->expects($this->once())
            ->method('build')
            ->willReturn($account);

        $results = $this->strategy->process($entity);

        $this->assertCount(3, $results);
        $this->assertEquals($account, $results[0]);
    }

    public function testSavePreviousAccountWhenIsParentCustomerWithAccount()
    {
        $entity = new Customer();
        $parent = new Customer();

        $account = new Account();
        $previousAccount = new Account();

        $parent->setAccount($account);
        $entity->setParent($parent);
        $entity->setAccount($previousAccount);

        $rootCustomerAssociation = new CustomerAssociation();
        $rootCustomerAssociation->setTarget($previousAccount);

        $customerAssociation = new CustomerAssociation();
        $customerAssociation->setTarget($account);

        $this->manager
            ->expects($this->exactly(2))
            ->method('getAccountCustomerByTarget')
            ->willReturnOnConsecutiveCalls($rootCustomerAssociation, $customerAssociation);

        $results = $this->strategy->process($entity);

        $this->assertCount(2, $results);
        $this->assertEquals($account, $results[0]->getAccount());
        $this->assertEquals($previousAccount, $results[0]->getPreviousAccount());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|AccountBuilder
     */
    private function createAccountBuilderMock()
    {
        return $this->createMock(AccountBuilder::class);
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
