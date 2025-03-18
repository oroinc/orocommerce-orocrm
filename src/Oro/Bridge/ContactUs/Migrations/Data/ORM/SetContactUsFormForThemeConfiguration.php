<?php

namespace Oro\Bridge\ContactUs\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CMSBundle\Entity\ContentWidget;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\FrontendBundle\Migrations\Data\ORM\LoadGlobalThemeConfigurationData;
use Oro\Bundle\LayoutBundle\Layout\Extension\ThemeConfiguration as LayoutThemeConfiguration;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\ThemeBundle\DependencyInjection\Configuration;
use Oro\Bundle\ThemeBundle\Entity\ThemeConfiguration;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadAdminUserData;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Sets contact_us_form widget for theme configuration
 */
class SetContactUsFormForThemeConfiguration extends AbstractFixture implements
    DependentFixtureInterface,
    ContainerAwareInterface
{
    use ContainerAwareTrait;

    public const string CONTACT_US_FORM = 'contact_us_form';

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $organization = $manager->getRepository(Organization::class)->getFirst();
        $themeConfiguration = $this->getThemeConfiguration($manager);
        if (!$themeConfiguration) {
            return;
        }

        $contentWidget = $manager->getRepository(ContentWidget::class)->findOneBy([
            'name' => self::CONTACT_US_FORM,
            'organization' => $organization
        ]);

        if (!$contentWidget) {
            return;
        }

        $key = LayoutThemeConfiguration::buildOptionKey('contact_us', self::CONTACT_US_FORM);
        if (!$themeConfiguration->getConfigurationOption($key)) {
            $themeConfiguration->addConfigurationOption($key, $contentWidget->getId());
            $manager->flush();
        }
    }

    protected function getThemeConfiguration(ObjectManager $manager): ?ThemeConfiguration
    {
        /** @var ConfigManager $configManager */
        $configManager = $this->container->get('oro_config.global');
        $value = $configManager->get(Configuration::getConfigKeyByName(Configuration::THEME_CONFIGURATION));
        if (!$value) {
            return null;
        }

        return $manager->getRepository(ThemeConfiguration::class)->find($value);
    }

    public function getDependencies(): array
    {
        return [
            LoadAdminUserData::class,
            LoadGlobalThemeConfigurationData::class,
            LoadContactUsFormContentWidgetData::class
        ];
    }
}
