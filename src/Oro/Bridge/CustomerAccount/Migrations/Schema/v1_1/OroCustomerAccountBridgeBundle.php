<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCustomerAccountBridgeBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(
                'Oro\Bundle\CustomerBundle\Entity\Customer',
                'previous_account',
                'datagrid',
                'is_visible',
                DatagridScope::IS_VISIBLE_FALSE
            )
        );
        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(
                'Oro\Bundle\CustomerBundle\Entity\Customer',
                'previous_account',
                'datagrid',
                'show_filter',
                false
            )
        );
        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(
                'Oro\Bundle\CustomerBundle\Entity\Customer',
                'previous_account',
                'extend',
                'target_field',
                'name'
            )
        );
    }
}
