<?php

namespace Oro\Bridge\CustomerSales\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SalesBundle\Migration\Extension\Customers\LeadExtensionAwareInterface;
use Oro\Bundle\SalesBundle\Migration\Extension\Customers\LeadExtensionTrait;
use Oro\Bundle\SalesBundle\Migration\Extension\Customers\OpportunityExtensionAwareInterface;
use Oro\Bundle\SalesBundle\Migration\Extension\Customers\OpportunityExtensionTrait;

class OroCustomerSalesBridgeBundle implements Migration, OpportunityExtensionAwareInterface, LeadExtensionAwareInterface
{
    use LeadExtensionTrait, OpportunityExtensionTrait;

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->leadExtension->addCustomerAssociation($schema, 'oro_account');
        $this->opportunityExtension->addCustomerAssociation($schema, 'oro_account');
    }
}
