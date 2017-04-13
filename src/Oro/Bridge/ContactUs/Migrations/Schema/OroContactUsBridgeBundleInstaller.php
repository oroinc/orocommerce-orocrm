<?php

namespace Oro\Bridge\ContactUs\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;

class OroContactUsBridgeBundleInstaller implements
    Installation,
    ExtendExtensionAwareInterface
{
    /** @var ExtendExtension */
    protected $extendExtension;

    /**
     * {@inheritdoc}
     */
    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

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
        $this->extendExtension->addManyToOneRelation(
            $schema,
            'orocrm_contactus_request',
            'customer_user',
            'oro_customer_user',
            'id',
            [
                'extend' => [
                    'owner' => ExtendScope::OWNER_CUSTOM
                ]
            ]
        );
    }
}
