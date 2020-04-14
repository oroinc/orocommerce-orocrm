<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bridge\ContactUs\Form\Type\ContactRequestType;
use Oro\Bridge\ContactUs\Tests\Unit\Stub\ContactRequestStub;
use Oro\Bundle\ContactUsBundle\Entity\ContactReason;
use Oro\Bundle\ContactUsBundle\Form\Type\ContactRequestType as BaseContactRequestType;
use Oro\Bundle\ContactUsBundle\Tests\Unit\Stub\ContactReasonStub;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Component\Testing\Unit\EntityTrait;
use Oro\Component\Testing\Unit\Form\Type\Stub\EntityType as EntityTypeStub;
use Oro\Component\Testing\Unit\PreloadedExtension;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Test\TypeTestCase;

class ContactRequestTypeTest extends TypeTestCase
{
    use EntityTrait;

    /**
     * @var ContactRequestType
     */
    protected $type;

    /**
     * @var TokenAccessorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $tokenAccessor;

    /**
     * @var LocalizationHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $localizationHelper;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->tokenAccessor = $this->createMock(TokenAccessorInterface::class);
        $this->localizationHelper = $this->createMock(LocalizationHelper::class);

        $this->type = new ContactRequestType($this->tokenAccessor, $this->localizationHelper);

        parent::setUp();
    }

    public function testSubmit()
    {
        $this->tokenAccessor->expects($this->once())
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

    public function testPreSetDataListener()
    {
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
        $this->tokenAccessor->expects($this->once())
            ->method('getUser')
            ->willReturn($customerUser);

        $this->localizationHelper->expects($this->once())
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
        $organization = new Organization();
        $organization->setName('OroCRM');
        $customerUser = new \stdClass;
        $this->tokenAccessor->expects($this->once())
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
     * {@inheritdoc}
     */
    protected function getExtensions()
    {
        $entityType = new EntityTypeStub(['test_contact_reason' => $this->getContactReason()]);

        return [
            new PreloadedExtension(
                [
                    $this->type,
                    EntityType::class => $entityType,
                ],
                []
            ),
        ];
    }

    /**
     * @return ContactReason|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getContactReason()
    {
        $contactReason = new ContactReasonStub('Some title');
        $contactReason->setTitles(new ArrayCollection());

        return $contactReason;
    }
}
