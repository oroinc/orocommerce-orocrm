<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;

class OroCustomerLifetimeField implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        if ($schema->hasTable('oro_account')) {
            $this->createLifetimeFields($schema);
        }
    }

    /**
     * @param Schema $schema
     */
    protected function createLifetimeFields(Schema $schema)
    {
        $table = $schema->getTable('oro_account');
        $table->addColumn(
            'lifetime',
            'money',
            [
                OroOptions::KEY => [
                    'extend' => [
                        'owner' => ExtendScope::OWNER_CUSTOM,
                        'is_extend' => true,
                    ],
                    'datagrid' => ['is_visible' => false],
                    'form' => ['is_enabled' => false,],
                    'view' => ['is_displayable' => false],
                    'merge' => ['display' => false],
                    'dataaudit' => ['auditable' => true],
                    'importexport' => [
                        'full' => true,
                        'order' => 15
                    ]
                ],
            ]
        );
    }
}
