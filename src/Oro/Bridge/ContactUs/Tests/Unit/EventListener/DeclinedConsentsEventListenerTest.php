<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Oro\Bridge\ContactUs\EventListener\DeclinedConsentsEventListener;
use Oro\Bridge\ContactUs\Helper\ContactRequestHelper;
use Oro\Bundle\ConsentBundle\Entity\ConsentAcceptance;
use Oro\Bundle\ConsentBundle\Event\DeclinedConsentsEvent;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Component\Testing\Unit\EntityTrait;

class DeclinedConsentsEventListenerTest extends \PHPUnit\Framework\TestCase
{
    use EntityTrait;

    /**
     * @var DeclinedConsentsEventListener
     */
    private $listener;

    /**
     * @var ContactRequestHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contactRequestHelper;

    /**
     * @var ObjectManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $entityManager;

    /**
     * @var ManagerRegistry|\PHPUnit\Framework\MockObject\MockObject
     */
    private $registry;

    protected function setUp(): void
    {
        $this->contactRequestHelper = $this->createMock(ContactRequestHelper::class);
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(ObjectManager::class);

        $this->listener = new DeclinedConsentsEventListener(
            $this->contactRequestHelper,
            $this->registry
        );
    }

    public function testOnDecline()
    {
        $this->mockRegistry();

        $declinedConsent = $this->getEntity(ConsentAcceptance::class, ['id' => 1]);
        $customerUser = $this->getEntity(CustomerUser::class, ['id' => 1]);
        $contactRequest = $this->getEntity(ContactRequest::class, ['id' => 1]);

        $this->contactRequestHelper->expects($this->once())
            ->method('createContactRequest')
            ->with($declinedConsent, $customerUser)
            ->willReturn($contactRequest);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($contactRequest);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $event = new DeclinedConsentsEvent([$declinedConsent], $customerUser);

        $this->listener->onDecline($event);
    }

    private function mockRegistry(): void
    {
        $this->registry->method('getManagerForClass')
            ->with(ContactRequest::class)
            ->willReturn($this->entityManager);
    }
}
