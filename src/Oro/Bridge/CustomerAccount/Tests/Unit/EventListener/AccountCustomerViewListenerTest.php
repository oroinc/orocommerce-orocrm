<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\EventListener;

use Oro\Bridge\CustomerAccount\EventListener\AccountCustomerViewListener;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Customer as SalesCustomer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Bundle\UIBundle\View\ScrollData;
use Twig\Environment;

class AccountCustomerViewListenerTest extends \PHPUnit\Framework\TestCase
{
    private AccountCustomerManager|\PHPUnit\Framework\MockObject\MockObject $accountCustomerManager;

    private AccountCustomerViewListener $listener;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->accountCustomerManager = $this->createMock(AccountCustomerManager::class);
        $this->listener = new AccountCustomerViewListener($this->accountCustomerManager);
    }

    public function testOnViewWhenNoCustomer(): void
    {
        $env = $this->createMock(Environment::class);
        $scrollData = new ScrollData();
        $entity = new \stdClass();
        $event = new BeforeListRenderEvent($env, $scrollData, $entity);
        $this->accountCustomerManager->expects(self::never())
            ->method('getAccountCustomerByTarget');
        $env->expects(self::never())
            ->method('render');

        $this->listener->onView($event);
        self::assertEmpty($scrollData->getData());
    }

    public function testOnView(): void
    {
        $env = $this->createMock(Environment::class);
        $scrollData = new ScrollData([ScrollData::DATA_BLOCKS => [[ScrollData::SUB_BLOCKS => [[]]]]]);
        $entity = new Customer();
        $event = new BeforeListRenderEvent($env, $scrollData, $entity);
        $salesCustomer = new SalesCustomer();
        $this->accountCustomerManager->expects(self::once())
            ->method('getAccountCustomerByTarget')
            ->with($entity)
            ->willReturn($salesCustomer);
        $template = '<div>SomeTemplate</div>';
        $env->expects(self::once())
            ->method('render')
            ->with(
                '@OroCustomerAccountBridge/Customer/accountView.html.twig',
                ['salesCustomer' => $salesCustomer]
            )
            ->willReturn($template);

        $this->listener->onView($event);
        self::assertEquals(
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
