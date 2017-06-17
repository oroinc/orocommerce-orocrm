<?php

namespace Oro\Bridge\ContactUs\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Bundle\WebsiteBundle\Manager\WebsiteManager;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\ContactUsBundle\Form\Type\ContactRequestType as BaseContactRequestType;
use Oro\Bundle\ContactUsBundle\Entity\Repository\ContactReasonRepository;

class ContactRequestType extends AbstractType
{
    /** @var TokenAccessorInterface */
    protected $tokenAccessor;

    /** @var WebsiteManager */
    protected $websiteManager;

    /**
     * @param TokenAccessorInterface $tokenAccessor
     * @param WebsiteManager         $websiteManager
     */
    public function __construct(TokenAccessorInterface $tokenAccessor, WebsiteManager $websiteManager)
    {
        $this->tokenAccessor = $tokenAccessor;
        $this->websiteManager = $websiteManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($builder->has('customer_user')) {
            $builder->remove('customer_user');
        }
        $builder->remove('submit');

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $contactRequest = $event->getData();

                // update data only for new contact requests
                if (!$contactRequest instanceof ContactRequest || null !== $contactRequest->getId()) {
                    return;
                }

                $loggedUser = $this->tokenAccessor->getUser();
                if (null === $loggedUser) { // todo remove in scope of BB-9269
                    $website = $this->websiteManager->getCurrentWebsite();
                    $contactRequest->setOwner($website->getOrganization());
                }
                if (!$loggedUser instanceof CustomerUser) {
                    return;
                }
                $contactRequest->setEmailAddress($loggedUser->getEmail());
                $contactRequest->setFirstName($loggedUser->getFirstName());
                $contactRequest->setLastName($loggedUser->getLastName());
                $contactRequest->setOrganizationName($loggedUser->getOrganization()->getName());
                $contactRequest->setCustomerUser($loggedUser);
            }
        );

        $builder->add(
            'organizationName',
            TextType::class,
            ['required' => false, 'label' => 'oro.contactus.contactrequest.organization_name.label']
        );
        $builder->add(
            'preferredContactMethod',
            ChoiceType::class,
            [
                'choices' => [
                    ContactRequest::CONTACT_METHOD_BOTH => ContactRequest::CONTACT_METHOD_BOTH,
                    ContactRequest::CONTACT_METHOD_PHONE => ContactRequest::CONTACT_METHOD_PHONE,
                    ContactRequest::CONTACT_METHOD_EMAIL => ContactRequest::CONTACT_METHOD_EMAIL,
                ],
                'required' => true,
                'label' => 'oro.contactus.contactrequest.preferred_contact_method.label',
            ]
        );
        $builder->add(
            'contactReason',
            'entity',
            [
                'class' => 'OroContactUsBundle:ContactReason',
                'property' => 'label',
                'empty_value' => 'oro.contactus.contactrequest.choose_contact_reason.label',
                'required' => false,
                'label' => 'oro.contactus.contactrequest.contact_reason.label',
                'query_builder' => function (ContactReasonRepository $er) {
                    return $er->getExistedContactReasonsQB();
                },
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ContactRequest::class,
                'dataChannelField' => false,
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return BaseContactRequestType::class;
    }
}
