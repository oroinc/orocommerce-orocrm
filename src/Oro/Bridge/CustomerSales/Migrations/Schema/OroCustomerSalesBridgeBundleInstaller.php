<?php

namespace Oro\Bridge\CustomerSales\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SalesBundle\Migration\Extension\Customers\OpportunityExtensionAwareInterface;
use Oro\Bundle\SalesBundle\Migration\Extension\Customers\OpportunityExtensionTrait;

class OroCustomerSalesBridgeBundleInstaller implements
    Installation,
    OpportunityExtensionAwareInterface
{
    use OpportunityExtensionTrait;

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->opportunityExtension->addCustomerAssociation($schema, 'oro_account');
    }
}
