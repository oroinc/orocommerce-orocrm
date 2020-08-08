<?php

namespace Oro\Bridge\ContactUs\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroContactUsBridgeBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries): void
    {
        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(
                ContactRequest::class,
                'customer_user',
                'frontend',
                'is_editable',
                false
            )
        );
    }
}
