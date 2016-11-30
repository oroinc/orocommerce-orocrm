<?php

namespace Oro\Bridge\CustomerAccount\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\SalesBundle\EntityConfig\CustomerScope;

class CustomerAccountGridListener
{
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
        $associationName = ExtendHelper::buildAssociationName(
            'Oro\Bundle\CustomerBundle\Entity\Account',
            CustomerScope::ASSOCIATION_KIND
        );
        $condition = sprintf('IDENTITY(o.%s) = :customer_id', $associationName);

        $config->offsetSetByPath('[source][query][where][and][0]', $condition);
        $config->offsetSetByPath('[source][bind_parameters][0]', 'customer_id');
    }
}
