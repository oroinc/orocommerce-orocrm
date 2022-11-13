<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bridge\CustomerAccount\Manager\Strategy\AssignRootStrategy;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\SalesBundle\Tests\Unit\Fixture\CustomerStub as CustomerAssociation;

class AssignRootStrategyTest extends \PHPUnit\Framework\TestCase
{
    /** @var AccountCustomerManager|\PHPUnit\Framework\MockObject\MockObject */
    private $manager;

    /** @var AccountBuilder|\PHPUnit\Framework\MockObject\MockObject */
    private $builder;

    /** @var AssignRootStrategy */
    private $strategy;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(AccountCustomerManager::class);
        $this->builder = $this->createMock(AccountBuilder::class);

        $this->strategy = new AssignRootStrategy(
            $this->builder,
            $this->manager,
            $this->createMock(LifetimeProcessor::class)
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

        $this->manager->expects(self::any())
            ->method('getAccountCustomerByTarget')
            ->willReturn($customerAssociation);
        $this->builder->expects(self::any())
            ->method('build')
            ->willReturn(new Account());

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

        $this->manager->expects(self::any())
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

        $this->manager->expects($this->exactly(2))
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

        $this->manager->expects($this->exactly(2))
            ->method('getAccountCustomerByTarget')
            ->willReturnOnConsecutiveCalls($rootCustomerAssociation, $customerAssociation);

        $results = $this->strategy->process($entity);

        $this->assertCount(2, $results);
        $this->assertEquals($account, $results[0]->getAccount());
        $this->assertEquals($previousAccount, $results[0]->getPreviousAccount());
    }
}
