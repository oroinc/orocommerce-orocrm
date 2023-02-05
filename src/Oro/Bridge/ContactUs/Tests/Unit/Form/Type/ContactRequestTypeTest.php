<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bridge\ContactUs\Form\Type\ContactRequestType;
use Oro\Bridge\ContactUs\Tests\Unit\Stub\ContactRequestStub;
use Oro\Bundle\ContactUsBundle\Entity\ContactReason;
use Oro\Bundle\ContactUsBundle\Entity\Repository\ContactReasonRepository;
use Oro\Bundle\ContactUsBundle\Form\Type\ContactRequestType as BaseContactRequestType;
use Oro\Bundle\ContactUsBundle\Tests\Unit\Stub\ContactReasonStub;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Component\Testing\Unit\EntityTrait;
use Oro\Component\Testing\Unit\Form\Type\Stub\EntityTypeStub;
use Oro\Component\Testing\Unit\PreloadedExtension;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Test\TypeTestCase;

class ContactRequestTypeTest extends TypeTestCase
{
    use EntityTrait;

    private ContactRequestType $type;

    /** @var ManagerRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $registry;

    /** @var TokenAccessorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $tokenAccessor;

    /** @var LocalizationHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $localizationHelper;

    public function testSubmit()
    {
        $this->registry->expects(self::once())
            ->method('getRepository')
            ->with(ContactReason::class)
            ->willReturn($this->getContactReasonRepository());

        $this->tokenAccessor->expects(self::once())
            ->method('getUser')
            ->willReturn(null);

        $contactRequest = $this->getEntity(ContactRequestStub::class);
        $form = $this->factory->create(
            ContactRequestType::class,
            $contactRequest
        );

        $form->submit(
            [
                'organizationName' => 'OroCRM',
                'firstName' => 'Amanda',
                'lastName' => 'Cole',
                'emailAddress' => 'AmandaRCole@example.org',
                'preferredContactMethod' => 'oro.contactus.contactrequest.method.phone',
                'contactReason' => 'test_contact_reason',
            ]
        );

        $expected = new ContactRequestStub();
        $expected->setFirstName('Amanda');
        $expected->setLastName('Cole');
        $expected->setEmailAddress('AmandaRCole@example.org');
        $expected->setOrganizationName('OroCRM');
        $expected->setPreferredContactMethod('oro.contactus.contactrequest.method.phone');
        $expected->setContactReason($this->getContactReason());

        $this->assertEquals($expected, $contactRequest);
    }

    private function getContactReasonRepository(): ContactReasonRepository
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->expects(self::once())
            ->method('getResult')
            ->willReturn($this->getContactReason());

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $contactReasonRepository = $this->createMock(ContactReasonRepository::class);
        $contactReasonRepository->expects(self::once())
            ->method('createExistingContactReasonsQB')
            ->willReturn($qb);

        return $contactReasonRepository;
    }

    private function getContactReason(): ContactReason
    {
        $contactReason = new ContactReasonStub('Some title');
        $contactReason->setTitles(new ArrayCollection());

        return $contactReason;
    }

    public function testPreSetDataListener()
    {
        $this->registry->expects(self::once())
            ->method('getRepository')
            ->with(ContactReason::class)
            ->willReturn($this->getContactReasonRepository());

        $organization = new Organization();
        $organization->setName('OroCRM');
        /** @var CustomerUser $customerUser */
        $customerUser = $this->getEntity(
            CustomerUser::class,
            [
                'firstName' => 'Amanda',
                'lastName' => 'Cole',
                'email' => 'AmandaRCole@example.org',
                'organization' => $organization,
            ]
        );
        $this->tokenAccessor->expects(self::once())
            ->method('getUser')
            ->willReturn($customerUser);

        $this->localizationHelper->expects(self::once())
            ->method('getLocalizedValue')
            ->with(new ArrayCollection())
            ->willReturn('Some title');
        $contactRequest = $this->getEntity(ContactRequestStub::class);

        $form = $this->factory->create(
            ContactRequestType::class,
            $contactRequest
        );
        $view = $form->createView();

        $expected = new ContactRequestStub();
        $expected->setFirstName('Amanda');
        $expected->setLastName('Cole');
        $expected->setEmailAddress('AmandaRCole@example.org');
        $expected->setOrganizationName('OroCRM');
        $expected->setCustomerUser($customerUser);

        $this->assertEquals($expected, $contactRequest);

        $this->assertEquals('Amanda', $view['firstName']->vars['value']);
        $this->assertEquals('Cole', $view['lastName']->vars['value']);
        $this->assertEquals('AmandaRCole@example.org', $view['emailAddress']->vars['value']);
        $this->assertEquals('OroCRM', $view['organizationName']->vars['value']);
    }

    public function testPreSetDataListenerWithWrongLoggedUser()
    {
        $this->registry->expects(self::once())
            ->method('getRepository')
            ->with(ContactReason::class)
            ->willReturn($this->getContactReasonRepository());

        $organization = new Organization();
        $organization->setName('OroCRM');
        $customerUser = new \stdClass;
        $this->tokenAccessor->expects(self::once())
            ->method('getUser')
            ->willReturn($customerUser);
        $contactRequest = $this->getEntity(ContactRequestStub::class);
        $form = $this->factory->create(
            ContactRequestType::class,
            $contactRequest
        );
        $view = $form->createView();

        $expected = new ContactRequestStub();

        $this->assertEquals($expected, $contactRequest);

        $this->assertEmpty($view['firstName']->vars['value']);
        $this->assertEmpty($view['lastName']->vars['value']);
        $this->assertEmpty($view['emailAddress']->vars['value']);
        $this->assertEmpty($view['organizationName']->vars['value']);
    }

    public function testGetParent()
    {
        $this->assertEquals(BaseContactRequestType::class, $this->type->getParent());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->tokenAccessor = $this->createMock(TokenAccessorInterface::class);
        $this->localizationHelper = $this->createMock(LocalizationHelper::class);

        $this->type = new ContactRequestType(
            $this->registry,
            $this->tokenAccessor,
            $this->localizationHelper
        );

        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension(
                [
                    $this->type,
                    EntityType::class => new EntityTypeStub([
                        'test_contact_reason' => $this->getContactReason()
                    ]),
                ],
                []
            ),
        ];
    }
}
