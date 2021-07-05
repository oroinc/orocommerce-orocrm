<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\EventListener;

use Oro\Bridge\ContactUs\EventListener\ContactRequestViewListener;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Bundle\UIBundle\View\ScrollData;
use Twig\Environment;

class ContactRequestViewListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ContactRequestViewListener */
    private $listener;

    protected function setUp(): void
    {
        $this->listener = new ContactRequestViewListener();
    }

    public function testOnView()
    {
        $customerUser = new CustomerUser();
        $entity = $this->getMockBuilder(ContactRequest::class)
            ->addMethods(['getCustomerUser'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getCustomerUser')
            ->willReturn($customerUser);
        $event = $this->createMock(BeforeListRenderEvent::class);
        $event->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);

        $twig = $this->createMock(Environment::class);
        $template = 'rendered_template';
        $twig->expects($this->once())
            ->method('render')
            ->with(
                '@OroContactUsBridge/ContactRequest/customerUser.html.twig',
                ['customerUser' => $customerUser]
            )
            ->willReturn($template);
        $event->expects($this->once())
            ->method('getEnvironment')
            ->willReturn($twig);
        $scrollData = $this->createMock(ScrollData::class);
        $scrollData->expects($this->once())
            ->method('addSubBlockData')
            ->with(0, 0, $template);
        $event->expects($this->once())
            ->method('getScrollData')
            ->willReturn($scrollData);

        $this->listener->onView($event);
    }
}
