<?php

namespace Oro\Bridge\ContactUs\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\FrontendBundle\Migrations\Data\ORM\LoadGlobalDefaultThemeConfigurationData;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\ThemeBundle\Entity\ThemeConfiguration;

/**
 * Sets contact_us_form widget for theme configuration for default theme
 */
class SetContactUsFormForDefaultThemeConfiguration extends SetContactUsFormForThemeConfiguration
{
    #[\Override]
    protected function getThemeConfigurations(ObjectManager $manager, Organization $organization): array
    {
        return $manager->getRepository(ThemeConfiguration::class)->findBy([
            'theme' => $this->getFrontendTheme(),
            'organization' => $organization
        ]);
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            ...parent::getDependencies(),
            LoadGlobalDefaultThemeConfigurationData::class,
            SetContactUsFormForThemeConfiguration::class
        ];
    }

    #[\Override]
    protected function getFrontendTheme(): ?string
    {
        return 'default';
    }
}
