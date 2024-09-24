<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema\v1_4;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCustomerLifetimeField implements Migration
{
    #[\Override]
    public function up(Schema $schema, QueryBag $queries)
    {
        if ($schema->hasTable('oro_customer')) {
            $this->modifyLifetimeFields($schema);
        }
    }

    protected function modifyLifetimeFields(Schema $schema)
    {
        $table = $schema->getTable('oro_customer');

        if (!$table->hasColumn('lifetime')) {
            return;
        }

        $table->getColumn('lifetime')
            ->setOptions([OroOptions::KEY => ['dataaudit' => ['auditable' => false]]]);
    }
}
