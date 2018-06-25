<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\EventListener;

use Oro\Bridge\CustomerAccount\EventListener\AccountViewListener;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Component\Testing\Unit\FormViewListenerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomerViewListenerTest extends FormViewListenerTestCase
{
    /** @var AccountViewListener */
    protected $listener;

    /** @var  Request|\PHPUnit\Framework\MockObject\MockObject */
    protected $request;

    /** @var  ConfigManager|\PHPUnit\Framework\MockObject\MockObject */
    protected $configManager;

    /** @var  RequestStack|\PHPUnit\Framework\MockObject\MockObject */
    protected $requestStack;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->request = $this->getRequest();
        /** @var RequestStack|\PHPUnit\Framework\MockObject\MockObject $requestStack */
        $this->requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');

        $this->configManager = $this->getMockBuilder('Oro\Bundle\ConfigBundle\Config\ConfigManager')
            ->disableOriginalConstructor()->getMock();

        $this->listener =
            new AccountViewListener(
                'Oro\Bundle\AccountBundle\Entity\Account',
                $this->doctrineHelper,
                $this->requestStack,
                $this->translator,
                $this->configManager
            );
    }

    public function testOnAccountUserViewWithEmptyRequest()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|BeforeListRenderEvent $event */
        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeListRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestStack->expects(self::any())->method('getCurrentRequest')->willReturn(null);

        $event->expects(self::never())
            ->method('getScrollData');

        $this->listener->onView($event);
    }

    public function testOnAccountView()
    {
        $this->request->expects(self::any())->method('get')->with('id')->willReturn(1);
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->request);

        $account = new Account();

        $this->doctrineHelper
            ->expects(self::once())
            ->method('getEntityReference')
            ->willReturn($account);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Twig_Environment $env */
        $env = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $env->expects(self::once())
            ->method('render')
            ->with('OroCustomerAccountBridgeBundle:Customer:view.html.twig', ['entity' => $account])
            ->willReturn('');

        $event = $this->getBeforeListRenderEvent();
        $event->expects(self::once())
            ->method('getEnvironment')
            ->willReturn($env);

        $this->listener->onView($event);
    }

    public function testOnAccountViewWithoutId()
    {
        $this->request->expects(self::any())->method('get')->with('id')->willReturn(null);
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->request);

        $customer = new Account();

        $this->doctrineHelper
            ->expects(self::never())
            ->method('getEntityReference')
            ->willReturn($customer);

        /** @var \PHPUnit\Framework\MockObject\MockObject|BeforeListRenderEvent $event */
        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeListRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())->method('getScrollData');

        $this->listener->onView($event);
    }

    public function testOnAccountViewWithoutEntity()
    {
        $this->request->expects(self::any())->method('get')->with('id')->willReturn(1);
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->request);

        $this->doctrineHelper
            ->expects(self::once())
            ->method('getEntityReference')
            ->willReturn(null);

        /** @var \PHPUnit\Framework\MockObject\MockObject|BeforeListRenderEvent $event */
        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeListRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())->method('getScrollData');

        $this->listener->onView($event);
    }

    public function testOnAccountViewWithEmptyRequest()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|BeforeListRenderEvent $event */
        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeListRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects(self::never())
            ->method('getScrollData');

        $this->listener->onView($event);
    }
}
