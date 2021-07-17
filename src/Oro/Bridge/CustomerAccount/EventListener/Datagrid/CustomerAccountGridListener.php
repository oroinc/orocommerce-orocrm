<?php

namespace Oro\Bridge\CustomerAccount\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmQueryConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\SalesBundle\EntityConfig\CustomerScope;

class CustomerAccountGridListener
{
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();

        $this->addWhere($config);
    }

    protected function addWhere(DatagridConfiguration $config)
    {
        $rootAlias = $config->getOrmQuery()->getRootAlias();
        if (!$rootAlias) {
            return;
        }

        $query = $config->getOrmQuery();
        $query->addLeftJoin(sprintf('%s.customerAssociation', $rootAlias), 'customerAssociation');

        $associationName = ExtendHelper::buildAssociationName(
            'Oro\Bundle\CustomerBundle\Entity\Customer',
            CustomerScope::ASSOCIATION_KIND
        );
        $condition = sprintf('customerAssociation.%s = :customer_id', $associationName);

        $config->offsetSetByPath(OrmQueryConfiguration::WHERE_AND_PATH . '[0]', $condition);
        $config->offsetSetByPath(DatagridConfiguration::DATASOURCE_BIND_PARAMETERS_PATH . '[0]', 'customer_id');
    }
}
