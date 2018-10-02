<?php

namespace Oro\Bridge\ContactUs\Form\Type;

use Oro\Bundle\ContactUsBundle\Entity\ContactReason;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\ContactUsBundle\Form\Type\ContactRequestType as BaseContactRequestType;
use Oro\Bundle\ContactUsBundle\Entity\Repository\ContactReasonRepository;

/**
 *  Represents ContactRequest type
 */
class ContactRequestType extends AbstractType
{
    /** @var TokenAccessorInterface */
    protected $tokenAccessor;

    /** @var LocalizationHelper */
    protected $localizationHelper;

    /**
     * @param TokenAccessorInterface $tokenAccessor
     */
    public function __construct(TokenAccessorInterface $tokenAccessor)
    {
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * @param LocalizationHelper $localizationHelper
     */
    public function setLocalizationHelper(LocalizationHelper $localizationHelper)
    {
        $this->localizationHelper = $localizationHelper;
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
                'choice_label' => function (ContactReason $entity) {
                    return $this->getLocalizationHelper()->getLocalizedValue($entity->getTitles());
                },
                'empty_value' => 'oro.contactus.contactrequest.choose_contact_reason.label',
                'required' => false,
                'label' => 'oro.contactus.contactrequest.contact_reason.label',
                'query_builder' => function (ContactReasonRepository $er) {
                    return $er->getExistedContactReasonsQB()
                        ->addSelect('titles')
                        ->leftJoin('cr.titles', 'titles');
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

    /**
     * @return LocalizationHelper
     */
    private function getLocalizationHelper()
    {
        if (!$this->localizationHelper) {
            throw new \LogicException(sprintf('No localization helper set for %s type', get_class($this)));
        }

        return $this->localizationHelper;
    }
}
