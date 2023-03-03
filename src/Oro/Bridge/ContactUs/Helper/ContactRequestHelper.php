<?php
declare(strict_types=1);

namespace Oro\Bridge\ContactUs\Helper;

use Oro\Bridge\ContactUs\DependencyInjection\Configuration;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConsentBundle\Entity\Consent;
use Oro\Bundle\ConsentBundle\Entity\ConsentAcceptance;
use Oro\Bundle\ContactUsBundle\Entity\ContactReason;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This helper creates contact request for consent that was unchecked
 */
class ContactRequestHelper
{
    private DoctrineHelper            $doctrineHelper;
    private ConfigManager             $configManager;
    private LocalizationHelper        $localizationHelper;
    private TranslatorInterface       $translator;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        DoctrineHelper $doctrineHelper,
        ConfigManager $configManager,
        LocalizationHelper $localizationHelper,
        TranslatorInterface $translator,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->doctrineHelper     = $doctrineHelper;
        $this->configManager      = $configManager;
        $this->localizationHelper = $localizationHelper;
        $this->translator         = $translator;
        $this->propertyAccessor   = $propertyAccessor;
    }

    public function createContactRequest(ConsentAcceptance $acceptance, CustomerUser $customerUser): ContactRequest
    {
        $contactReason = $this->getContactReason();

        $contactRequest = new ContactRequest();
        $contactRequest->setContactReason($contactReason);
        $contactRequest->setFirstName($customerUser->getFirstName());
        $contactRequest->setLastName($customerUser->getLastName());
        $contactRequest->setEmailAddress($customerUser->getEmail());

        if ($this->propertyAccessor->isWritable($contactRequest, 'customer_user')) {
            $this->propertyAccessor->setValue($contactRequest, 'customer_user', $customerUser);
        }
        if ($this->propertyAccessor->isWritable($contactRequest, 'website')) {
            $this->propertyAccessor->setValue($contactRequest, 'website', $customerUser->getWebsite());
        }

        $comment = $this->prepareComment($acceptance->getConsent());
        $contactRequest->setComment($comment);

        return $contactRequest;
    }

    protected function prepareComment(Consent $consent): string
    {
        $comment = $this->translator->trans(
            'oro.consent.declined.message',
            ['%consent%' => $this->localizationHelper->getLocalizedValue($consent->getNames())]
        );

        return $comment;
    }

    /**
     * @return ContactReason
     */
    private function getContactReason()
    {
        $configKey       = Configuration::getConfigKey(Configuration::CONSENT_CONTACT_REASON);
        $contactReasonId = $this->configManager->get($configKey);
        /** @var ContactReason $contactReason */
        $contactReason = $this->doctrineHelper->getEntityRepository(ContactReason::class)
            ->findOneBy(['id' => $contactReasonId]);

        return $contactReason;
    }
}
