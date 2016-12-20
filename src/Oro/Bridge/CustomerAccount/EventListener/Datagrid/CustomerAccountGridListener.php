<?php

namespace Oro\Bridge\CustomerAccount\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Tools\GridConfigurationHelper;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\SalesBundle\EntityConfig\CustomerScope;

class CustomerAccountGridListener
{
    protected $configurationHelper;

    /**
     * @param GridConfigurationHelper $configurationHelper
     */
    public function __construct(GridConfigurationHelper $configurationHelper)
    {
        $this->configurationHelper = $configurationHelper;
    }

    /**
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();

        $this->addWhere($config);
    }

    /**
     * @param DatagridConfiguration $config
     */
    protected function addWhere(DatagridConfiguration $config)
    {
        $rootAlias = $this->configurationHelper->getEntityRootAlias($config);
        if (!$rootAlias) {
            return;
        }

        $query = $config->offsetGetByPath('[source][query]', []);

        $query['join']['left'][] = [
            'join'  => sprintf('%s.customerAssociation', $rootAlias),
            'alias' => 'customerAssociation',
        ];
        $config->offsetSetByPath('[source][query]', $query);

        $associationName = ExtendHelper::buildAssociationName(
            'Oro\Bundle\CustomerBundle\Entity\Account',
            CustomerScope::ASSOCIATION_KIND
        );
        $condition = sprintf('customerAssociation.%s = :customer_id', $associationName);

        $config->offsetSetByPath('[source][query][where][and][0]', $condition);
        $config->offsetSetByPath('[source][bind_parameters][0]', 'customer_id');
    }
}
