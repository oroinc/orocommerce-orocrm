<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\EventListener;

use Symfony\Component\Form\FormView;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Oro\Component\Testing\Unit\FormViewListenerTestCase;
use Oro\Bundle\UIBundle\View\ScrollData;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bridge\CustomerAccount\EventListener\AccountFormViewListener;

class AccountFormViewListenerTest extends FormViewListenerTestCase
{
    /**
     * @var AccountFormViewListener
     */
    protected $listener;

    /** @var  Request|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->request = $this->getRequest();
        /** @var RequestStack|\PHPUnit_Framework_MockObject_MockObject $requestStack */
        $requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->any())->method('getCurrentRequest')->willReturn($this->request);
        $this->listener =
            new AccountFormViewListener(
                $this->doctrineHelper,
                $requestStack,
                'Oro\Bundle\AccountBundle\Entity\Account'
            );
    }

    public function testOnAccountUserViewWithEmptyRequest()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|BeforeListRenderEvent $event */
        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeListRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->never())
            ->method('getScrollData');

        $this->listener->onView($event);
    }

    public function testOnEdit()
    {
        $event = $this->getBeforeListRenderEvent();

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment $env */
        $env = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $env->expects($this->once())
            ->method('render')
            ->with('OroCustomerAccountBridgeBundle:Account:account_update.html.twig', ['form' => new FormView()])
            ->willReturn('');

        $event->expects($this->once())
            ->method('getEnvironment')
            ->willReturn($env);

        $event->expects($this->once())
            ->method('getFormView')
            ->willReturn(new FormView());

        $this->listener->onEdit($event);
    }

    public function testOnAccountView()
    {
        $this->request->expects($this->any())->method('get')->with('id')->willReturn(1);

        $customer = new Customer();

        $this->doctrineHelper
            ->expects($this->once())
            ->method('getEntityReference')
            ->willReturn($customer);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment $env */
        $env = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $env->expects($this->once())
            ->method('render')
            ->with('OroCustomerAccountBridgeBundle:Account:account_view.html.twig', ['entity' => $customer])
            ->willReturn('');

        $event = $this->getBeforeListRenderEvent();
        $event->expects($this->once())
            ->method('getEnvironment')
            ->willReturn($env);

        $this->listener->onView($event);
    }

    public function testOnAccountViewWithoutId()
    {
        $this->request->expects($this->any())->method('get')->with('id')->willReturn(null);

        $customer = new Customer();

        $this->doctrineHelper
            ->expects($this->never())
            ->method('getEntityReference')
            ->willReturn($customer);

        /** @var \PHPUnit_Framework_MockObject_MockObject|BeforeListRenderEvent $event */
        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeListRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->never())->method('getScrollData');

        $this->listener->onView($event);
    }

    public function testOnAccountViewWithoutEntity()
    {
        $this->request->expects($this->any())->method('get')->with('id')->willReturn(1);

        $this->doctrineHelper
            ->expects($this->once())
            ->method('getEntityReference')
            ->willReturn(null);

        /** @var \PHPUnit_Framework_MockObject_MockObject|BeforeListRenderEvent $event */
        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeListRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->never())->method('getScrollData');

        $this->listener->onView($event);
    }

    public function testOnAccountViewWithEmptyRequest()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|BeforeListRenderEvent $event */
        $event = $this->getMockBuilder('Oro\Bundle\UIBundle\Event\BeforeListRenderEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->never())
            ->method('getScrollData');

        $this->listener->onView($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ScrollData
     */
    protected function getScrollData()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|ScrollData $scrollData */
        $scrollData = $this->getMock('Oro\Bundle\UIBundle\View\ScrollData');

        $scrollData->expects($this->once())
            ->method('addSubBlockData');

        return $scrollData;
    }
}
