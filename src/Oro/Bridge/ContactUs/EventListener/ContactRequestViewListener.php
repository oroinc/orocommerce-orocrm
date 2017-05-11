<?php

namespace Oro\Bridge\ContactUs\EventListener;

use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;

class ContactRequestViewListener
{
    /**
     * {@inheritdoc}
     */
    public function onView(BeforeListRenderEvent $event)
    {
        /** @var ContactRequest $contactRequest */
        $contactRequest = $event->getEntity();
        if (!$contactRequest) {
            return;
        }

        $customerUser = $contactRequest->getCustomerUser();
        $template = $event->getEnvironment()->render(
            'OroContactUsBridgeBundle:ContactRequest:customerUser.html.twig',
            ['customerUser' => $customerUser]
        );
        $event->getScrollData()->addSubBlockData(0, 0, $template);
    }
}
