<?php

namespace Oro\Bridge\QuoteSales\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SaleBundle\Entity\Quote;
use Oro\Bundle\SalesBundle\Form\Type\OpportunitySelectType;

class UpdateOpportunityRelationFormType implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addQuery(
            new UpdateEntityConfigFieldValueQuery(
                Quote::class,
                'opportunity',
                'form',
                'form_type',
                OpportunitySelectType::class,
                'oro_sales_opportunity_select'
            )
        );

        $queries->addQuery(
            new UpdateEntityConfigFieldValueQuery(
                Quote::class,
                'opportunity',
                'form',
                'form_options',
                ['attr' => ['readonly' => true]],
                ['read_only' => true]
            )
        );
    }
}
