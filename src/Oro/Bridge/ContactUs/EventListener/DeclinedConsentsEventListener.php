<?php

namespace Oro\Bridge\ContactUs\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Oro\Bridge\ContactUs\Helper\ContactRequestHelper;
use Oro\Bundle\ConsentBundle\Event\DeclinedConsentsEvent;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;

/**
 * Event listener that creates contact request after customer decides to decline
 * his acceptance of consent
 */
class DeclinedConsentsEventListener
{
    /**
     * @var ContactRequestHelper
     */
    private $contactRequestHelper;

    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(ContactRequestHelper $contactRequestHelper, ManagerRegistry $registry)
    {
        $this->contactRequestHelper = $contactRequestHelper;
        $this->registry = $registry;
    }

    public function onDecline(DeclinedConsentsEvent $event): void
    {
        $declinedConsents = $event->getDeclinedConsents();
        $customerUser = $event->getCustomerUser();
        $entityManager = $this->getEntityManager();

        foreach ($declinedConsents as $declinedConsent) {
            $contactRequest = $this->contactRequestHelper->createContactRequest($declinedConsent, $customerUser);
            $entityManager->persist($contactRequest);
        }

        $entityManager->flush();
    }

    private function getEntityManager(): ObjectManager
    {
        return $this->registry->getManagerForClass(ContactRequest::class);
    }
}
