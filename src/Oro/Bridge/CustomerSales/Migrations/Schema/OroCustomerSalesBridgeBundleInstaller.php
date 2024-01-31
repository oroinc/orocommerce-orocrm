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
     * {@inheritDoc}
     */
    public function getMigrationVersion(): string
    {
        return 'v1_0';
    }

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        $this->customerExtension->addCustomerAssociation($schema, 'oro_customer');

        // before activity block which have 1000
        $options = new OroOptions();
        $options->set('customer', 'associated_opportunity_block_priority', 990);
        $schema->getTable('oro_customer')->addOption(OroOptions::KEY, $options);
    }
}
