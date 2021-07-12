<?php
declare(strict_types=1);

namespace Oro\Bridge\ContactUs\EventListener;

use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;

/**
 * Adds "Customer User" field to the contact request view page in the back-office.
 * @see @OroContactUsBridge/ContactRequest/customerUser.html.twig
 */
class ContactRequestViewListener
{
    /**
     * Adds "Customer User" field to the contact request view page in the back-office.
     * @see @OroContactUsBridge/ContactRequest/customerUser.html.twig
     */
    public function onView(BeforeListRenderEvent $event): void
    {
        /** @var ContactRequest $contactRequest */
        $contactRequest = $event->getEntity();
        if (!$contactRequest) {
            return;
        }

        $customerUser = $contactRequest->getCustomerUser();

        $template = $event->getEnvironment()->render(
            '@OroContactUsBridge/ContactRequest/customerUser.html.twig',
            ['customerUser' => $customerUser]
        );

        $event->getScrollData()->addSubBlockData(0, 0, $template);
    }
}
