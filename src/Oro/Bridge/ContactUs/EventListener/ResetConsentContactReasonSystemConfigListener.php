<?php

namespace Oro\Bridge\ContactUs\EventListener;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ContactUsBundle\Entity\ContactReason;

/**
 * Resets a value of the "oro_contact_us_bridge.consent_contact_reason" configuration option
 * when it is associated with a removed ContactReason entity.
 */
class ResetConsentContactReasonSystemConfigListener
{
    private const CONFIG_KEY = 'oro_contact_us_bridge.consent_contact_reason';

    private ConfigManager $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function onPreRemove(ContactReason $contactReason): void
    {
        if ($contactReason->getId() === (int)$this->configManager->get(self::CONFIG_KEY)) {
            $this->configManager->reset(self::CONFIG_KEY);
            $this->configManager->flush();
        }
    }
}
