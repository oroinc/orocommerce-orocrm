<?php

namespace Oro\Bridge\CustomerAccount\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;

class CustomerGridListener
{
    /**
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();

        $this->addColumn($config);
    }

    /**
     * @param DatagridConfiguration $config
     */
    protected function addColumn(DatagridConfiguration $config)
    {
        $config->offsetSetByPath(
            sprintf('[columns][%s][label]', 'account'),
            'oro.customer_account_bridge.account.label'
        );
        $config->offsetSetByPath(sprintf('[columns][%s][frontend_type]', 'account'), 'relation');
        $config->offsetSetByPath(
            sprintf('[columns][%s][inline_editing]', 'account'),
            [
                'enable' => true,
                'editor' => [
                    'view_options' => [
                        'value_field_name' => 'account',
                    ],
                ],
                'autocomplete_api_accessor' => [
                    'search_handler_name' => 'accounts',
                    'label_field_name' => 'name',
                ],
            ]
        );
    }
}
