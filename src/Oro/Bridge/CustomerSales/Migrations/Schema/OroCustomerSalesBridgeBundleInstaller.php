<?php

namespace Oro\Bridge\CustomerSales\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SalesBundle\Migration\Extension\CustomerExtensionAwareInterface;
use Oro\Bundle\SalesBundle\Migration\Extension\CustomerExtensionTrait;

class OroCustomerSalesBridgeBundleInstaller implements Installation, CustomerExtensionAwareInterface
{
    use CustomerExtensionTrait;

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
        $this->customerExtension->addCustomerAssociation($schema, 'oro_customer');

        $table = $schema->getTable('oro_customer');

        // before activity block which have 1000
        $options = new OroOptions();
        $options->set('customer', 'associated_opportunity_block_priority', 990);
        $table->addOption(OroOptions::KEY, $options);
    }
}
