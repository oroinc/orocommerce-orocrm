<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCustomerImportExport implements Migration
{
    /**
     * Modifies the given schema to apply necessary changes of a database
     * The given query bag can be used to apply additional SQL queries before and after schema changes
     *
     * @param Schema $schema
     * @param QueryBag $queries
     * @return void
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addQuery(
            new UpdateEntityConfigFieldValueQuery(
                Customer::class,
                'previous_account',
                'importexport',
                'excluded',
                true
            )
        );

        $queries->addQuery(
            new UpdateEntityConfigFieldValueQuery(
                Customer::class,
                'lifetime',
                'importexport',
                'excluded',
                true
            )
        );

        $queries->addQuery(
            new UpdateEntityConfigFieldValueQuery(
                Customer::class,
                'dataChannel',
                'importexport',
                'excluded',
                true
            )
        );
    }
}
