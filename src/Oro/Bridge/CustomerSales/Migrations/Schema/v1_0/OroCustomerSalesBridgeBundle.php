<?php

namespace Oro\Bridge\CustomerSales\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SalesBundle\Migration\Extension\Customers\OpportunityExtensionAwareInterface;
use Oro\Bundle\SalesBundle\Migration\Extension\Customers\OpportunityExtensionTrait;

class OroCustomerSalesBridgeBundle implements Migration, OpportunityExtensionAwareInterface
{
    use OpportunityExtensionTrait;

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->opportunityExtension->addCustomerAssociation($schema, 'oro_account');
    }
}
