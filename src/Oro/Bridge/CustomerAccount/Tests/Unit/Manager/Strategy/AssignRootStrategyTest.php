<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bridge\CustomerAccount\Manager\Strategy\AssignRootStrategy;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bundle\AccountBundle\Entity\Account;

class AssignRootStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssignRootStrategy
     */
    protected $strategy;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->markTestIncomplete('CRM-7187');

        $this->strategy = new AssignRootStrategy($this->createAccountBuilderMock());
    }

    public function testCorrectName()
    {
        $name = $this->strategy->getName();
        $this->assertEquals(AssignRootStrategy::NAME, $name);
    }

    public function testCreateNewEntitiesIfNoParentCustomer()
    {
        $entity = new Customer();
        $results = $this->strategy->process($entity);

        $this->assertCount(2, $results);
    }

    public function testGetParentValueIfIsParentCustomerWithAccount()
    {
        $entity = new Customer();
        $parent = new Customer();
        $account = new Account();
        $parent->setAccount($account);
        $entity->setParent($parent);
        $results = $this->strategy->process($entity);

        $this->assertCount(1, $results);
        $this->assertEquals($account, $results[0]->getAccount());
        $this->assertNull($results[0]->getPreviousAccount());
    }

    public function testCreateNewEntityIfIsParentCustomerWithoutAccount()
    {
        $accountBuilder = $this->createAccountBuilderMock();
        $account = new Account();
        $accountBuilder
            ->expects($this->once())
            ->method('build')
            ->willReturn($account);
        $this->strategy = new AssignRootStrategy($accountBuilder);

        $entity = new Customer();
        $parent = new Customer();
        $entity->setParent($parent);
        $results = $this->strategy->process($entity);

        $this->assertCount(2, $results);
        $this->assertEquals($account, $results[0]);
        $this->assertEquals($account, $results[1]->getAccount());
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
        $results = $this->strategy->process($entity);

        $this->assertCount(1, $results);
        $this->assertEquals($account, $results[0]->getAccount());
        $this->assertEquals($previousAccount, $results[0]->getPreviousAccount());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AccountBuilder
     */
    private function createAccountBuilderMock()
    {
        return $this->createMock(AccountBuilder::class);
    }
}
