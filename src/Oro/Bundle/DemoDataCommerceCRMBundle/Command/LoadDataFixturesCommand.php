<?php

namespace Oro\Bundle\DemoDataCommerceCRMBundle\Command;

use Oro\Bundle\MigrationBundle\Command\LoadDataFixturesCommand as BaseDataFixturesCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Override "oro:migration:data:load" command and add exclude sample data for Magento Bundle because it's not possible
 * to modify arguments of the command through command listener.
 *
 * @todo: Should be moved to console listener after: https://github.com/symfony/symfony/issues/19441
 */
class LoadDataFixturesCommand extends BaseDataFixturesCommand
{
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('fixtures-type') === self::DEMO_FIXTURES_TYPE) {
            $exclude = $input->getOption('exclude') ?: [];
            if (empty($exclude)) {
                // exclude Demo data loading for Magento and related Bundles
                $exclude = ['OroMagentoBundle', 'OroAbandonedCartBundle', 'OroMarketingCRMBridgeBundle'];
            }
            $input->setOption('exclude', $exclude);
        }

        return parent::execute($input, $output);
    }
}
