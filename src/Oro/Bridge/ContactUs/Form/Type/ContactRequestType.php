<?php

namespace Oro\Bridge\ContactUs\Form\Type;

use Oro\Bundle\ContactUsBundle\Entity\ContactReason;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\ContactUsBundle\Entity\Repository\ContactReasonRepository;
use Oro\Bundle\ContactUsBundle\Form\Type\ContactRequestType as BaseContactRequestType;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is used on the storefront for "contact us" form show to customer users and guests.
 * It extends \Oro\Bundle\ContactUsBundle\Form\Type\ContactRequestType form type.
 * @see \Oro\Bundle\ContactUsBundle\Form\Type\ContactRequestType parent form type
 * @see \Oro\Bridge\ContactUs\Controller\ContactRequestController usage in storefront controller
 * @see \Oro\Bridge\ContactUs\ContentWidget\ContactUsFormContentWidgetType usage in storefront widget
 */
class ContactRequestType extends AbstractType
{
    protected TokenAccessorInterface $tokenAccessor;

    protected LocalizationHelper $localizationHelper;

    public function __construct(TokenAccessorInterface $tokenAccessor, LocalizationHelper $localizationHelper)
    {
        $this->tokenAccessor = $tokenAccessor;
        $this->localizationHelper = $localizationHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            EntityType::class,
            [
                'class' => 'OroContactUsBundle:ContactReason',
                'choice_label' => function (ContactReason $entity) {
                    return $this->localizationHelper->getLocalizedValue($entity->getTitles());
                },
                'placeholder' => 'oro.contactus.contactrequest.choose_contact_reason.label',
                'required' => false,
                'label' => 'oro.contactus.contactrequest.contact_reason.label',
                'query_builder' => fn (ContactReasonRepository $er) => $er->createExistingContactReasonsWithTitlesQB(),
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ContactRequest::class,
                'dataChannelField' => false,
            ]
        );
    }

    public function getParent()
    {
        return BaseContactRequestType::class;
    }
}
