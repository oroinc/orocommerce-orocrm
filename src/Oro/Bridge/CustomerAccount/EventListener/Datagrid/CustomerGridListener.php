<?php

namespace Oro\Bridge\CustomerAccount\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;

class CustomerGridListener
{
    const CRM_ACCOUNT_NAME = 'crm_account_name';

    /**
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();

        $this->addSelect($config);
        $this->addJoin($config);
        $this->addColumn($config);
        $this->addSorter($config);
        $this->addFilter($config);
    }

    /**
     * @param DatagridConfiguration $config
     */
    protected function addSelect(DatagridConfiguration $config)
    {
        $config->offsetAddToArrayByPath(
            '[source][query][select]',
            [sprintf('%s AS %s', 'crm_account.name', self::CRM_ACCOUNT_NAME)]
        );
    }

    /**
     * @param DatagridConfiguration $config
     */
    protected function addJoin(DatagridConfiguration $config)
    {
        $config->offsetAddToArrayByPath(
            '[source][query][join][left]',
            [
                [
                    'join' => 'account.account',
                    'alias' => 'crm_account',
                ],
            ]
        );
    }

    /**
     * @param DatagridConfiguration $config
     */
    protected function addColumn(DatagridConfiguration $config)
    {
        $config->offsetSetByPath(
            sprintf('[columns][%s]', self::CRM_ACCOUNT_NAME),
            [
                'label' => 'oro.customer_account_bridge.account.label',
                'frontend_type' => 'relation',
                'inline_editing' => [
                    'enable' => true,
                    'editor' => [
                        'view_options' => [
                            'value_field_name' => 'account'
                        ]
                    ],
                    'autocomplete_api_accessor' => [
                        'search_handler_name' => 'accounts',
                        'label_field_name' => 'name',
                    ],
                ]
            ]
        );
    }

    /**
     * @param DatagridConfiguration $config
     */
    protected function addSorter(DatagridConfiguration $config)
    {
        $config->offsetSetByPath(
            sprintf('[sorters][columns][%s]', self::CRM_ACCOUNT_NAME),
            ['data_name' => 'account.account']
        );
    }

    /**
     * @param DatagridConfiguration $config
     */
    protected function addFilter(DatagridConfiguration $config)
    {
        $config->offsetSetByPath(
            sprintf('[filters][columns][%s]', self::CRM_ACCOUNT_NAME),
            ['type' => 'string', 'data_name' => self::CRM_ACCOUNT_NAME]
        );
    }
}
