<?php

namespace Oro\Bridge\ContactUs\EventListener;

use Oro\Bridge\ContactUs\DependencyInjection\Configuration;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\Event\ConfigSettingsUpdateEvent;
use Oro\Bundle\ContactUsBundle\Entity\ContactReason;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

/**
 * This class prepare system setting value before save and reset config for selected consent contact reason
 * if it was removed.
 */
class SystemConfigListener
{
    /** @var DoctrineHelper */
    private $doctrineHelper;

    /** @var ConfigManager */
    private $configManager;

    public function __construct(DoctrineHelper $doctrineHelper, ConfigManager $configManager)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->configManager = $configManager;
    }

    public function onFormPreSetData(ConfigSettingsUpdateEvent $event)
    {
        $settingsKey = Configuration::getConfigKey(
            Configuration::CONSENT_CONTACT_REASON,
            ConfigManager::SECTION_VIEW_SEPARATOR
        );
        $settings = $event->getSettings();
        if (is_array($settings) && isset($settings[$settingsKey]['value'])) {
            $settings[$settingsKey]['value'] = $this->doctrineHelper->getEntityManager(ContactReason::class)
                ->find(ContactReason::class, $settings[$settingsKey]['value']);
            $event->setSettings($settings);
        }
    }

    public function onSettingsSaveBefore(ConfigSettingsUpdateEvent $event)
    {
        $settings = $event->getSettings();

        if (!array_key_exists('value', $settings)) {
            return;
        }

        if (!$settings['value'] instanceof ContactReason) {
            return;
        }

        $contactReason = $settings['value'];
        $settings['value'] = $contactReason->getId();
        $event->setSettings($settings);
    }

    public function onPreRemove(ContactReason $contactReason)
    {
        $configKey = Configuration::getConfigKey(Configuration::CONSENT_CONTACT_REASON);
        $consentContactReasonId = (int) $this->configManager->get($configKey);

        if ($contactReason->getId() === $consentContactReasonId) {
            $this->configManager->reset($configKey);
            $this->configManager->flush();
        }
    }
}
