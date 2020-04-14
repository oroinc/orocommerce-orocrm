<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\EventListener;

use Oro\Bridge\CustomerAccount\EventListener\AccountCustomerViewListener;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Customer as SalesCustomer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Bundle\UIBundle\View\ScrollData;

class AccountCustomerViewListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AccountCustomerManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $accountCustomerManager;

    /**
     * @var AccountCustomerViewListener
     */
    private $listener;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->accountCustomerManager = $this->createMock(AccountCustomerManager::class);
        $this->listener = new AccountCustomerViewListener($this->accountCustomerManager);
    }

    public function testOnViewWhenNoCustomer()
    {
        /** @var \Twig_Environment|\PHPUnit\Framework\MockObject\MockObject $env */
        $env = $this->createMock(\Twig_Environment::class);
        $scrollData = new ScrollData();
        $entity = new \stdClass();
        $event = new BeforeListRenderEvent($env, $scrollData, $entity);
        $this->accountCustomerManager->expects($this->never())
            ->method('getAccountCustomerByTarget');
        $env->expects($this->never())
            ->method('render');

        $this->listener->onView($event);
        $this->assertEmpty($scrollData->getData());
    }

    public function testOnView()
    {
        /** @var \Twig_Environment|\PHPUnit\Framework\MockObject\MockObject $env */
        $env = $this->createMock(\Twig_Environment::class);
        $scrollData = new ScrollData([ScrollData::DATA_BLOCKS => [[ScrollData::SUB_BLOCKS => [[]]]]]);
        $entity = new Customer();
        $event = new BeforeListRenderEvent($env, $scrollData, $entity);
        $salesCustomer = new SalesCustomer();
        $this->accountCustomerManager->expects($this->once())
            ->method('getAccountCustomerByTarget')
            ->with($entity)
            ->willReturn($salesCustomer);
        $template = '<div>SomeTemplate</div>';
        $env->expects($this->once())
            ->method('render')
            ->with(
                'OroCustomerAccountBridgeBundle:Customer:accountView.html.twig',
                ['salesCustomer' => $salesCustomer]
            )
            ->willReturn($template);

        $this->listener->onView($event);
        $this->assertEquals(
            [
                ScrollData::DATA_BLOCKS => [
                    [
                        ScrollData::SUB_BLOCKS => [
                            [
                                ScrollData::DATA => [
                                    $template
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $scrollData->getData()
        );
    }
}
