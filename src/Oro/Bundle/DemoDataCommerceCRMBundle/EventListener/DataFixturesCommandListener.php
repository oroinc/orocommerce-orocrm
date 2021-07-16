<?php

namespace Oro\Bundle\DemoDataCommerceCRMBundle\EventListener;

use Oro\Bundle\MigrationBundle\Command\LoadDataFixturesCommand;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * Override the default value of the option "exclude" for the "oro:migration:data:load" command
 * to exclude sample data from Magento Bundle.
 */
class DataFixturesCommandListener
{
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        if (!$command || $command->getName() !== LoadDataFixturesCommand::getDefaultName()) {
            return;
        }

        $input = $event->getInput();
        if ($input->getOption('fixtures-type') !== LoadDataFixturesCommand::DEMO_FIXTURES_TYPE) {
            return;
        }

        $definition = $command->getDefinition();
        if (!$definition->hasOption('exclude')) {
            return;
        }

        // exclude Demo data loading for Magento and related Bundles
        $option = $definition->getOption('exclude');
        $option->setDefault(
            array_merge(
                (array) $option->getDefault(),
                ['OroMagentoBundle', 'OroMarketingCRMBridgeBundle']
            )
        );
    }
}
