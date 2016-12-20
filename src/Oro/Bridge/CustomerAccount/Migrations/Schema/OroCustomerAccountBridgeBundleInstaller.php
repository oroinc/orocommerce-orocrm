<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\ActivityListBundle\Migration\Extension\ActivityListExtension;
use Oro\Bundle\ActivityListBundle\Migration\Extension\ActivityListExtensionAwareInterface;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityConfigBundle\Entity\ConfigModel;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManager;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;

class OroCustomerAccountBridgeBundleInstaller implements
    Installation,
    ExtendExtensionAwareInterface,
    ActivityListExtensionAwareInterface
{
    /** @var ActivityListExtension */
    protected $activityListExtension;

    /** @var ExtendExtension */
    protected $extendExtension;

    /**
     * {@inheritdoc}
     */
    public function setActivityListExtension(ActivityListExtension $activityListExtension)
    {
        $this->activityListExtension = $activityListExtension;
    }

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
        return 'v1_2';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        if ($schema->hasTable('oro_account') && $schema->hasTable('orocrm_account')) {
            $this->createFields($schema);
            $this->addInheritanceTargets($schema);
            $this->createLifetimeFields($schema);
            $this->createChannelFields($schema);
        }
    }

    /**
     * @param Schema $schema
     */
    public function addInheritanceTargets(Schema $schema)
    {
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_account',
            ['account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_order',
            ['account', 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_account_user',
            ['account', 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_shopping_list',
            ['account', 'account']
        );

        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_sale_quote',
            ['account', 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_rfp_request',
            ['account', 'account']
        );
    }

    /**
     * @param Schema $schema
     */
    protected function createFields(Schema $schema)
    {
        $this->extendExtension->addManyToOneRelation(
            $schema,
            'oro_account',
            'account',
            'orocrm_account',
            'name',
            [
                'entity' => ['label' => 'oro.account.entity_label'],
                'extend' => [
                    'owner' => ExtendScope::OWNER_CUSTOM,
                    'is_extend' => true,
                ],
                'form' => [
                    'is_enabled' => true,
                    'form_type' => 'oro_account_select'
                ],
                'datagrid' => [
                    'is_visible' => DatagridScope::IS_VISIBLE_TRUE,
                    'show_filter' => true,
                ],
                'view' => ['is_displayable' => false],
                'merge' => ['display' => false],
                'dataaudit' => ['auditable' => false]
            ]
        );
        $this->extendExtension->addManyToOneRelation(
            $schema,
            'oro_account',
            'previous_account',
            'orocrm_account',
            'name',
            [
                'entity' => ['label' => 'oro.customer_account_bridge.previous_account.entity_label'],
                'extend' => [
                    'owner' => ExtendScope::OWNER_CUSTOM,
                    'is_extend' => true,
                ],
                'datagrid' => [
                    'is_visible' => DatagridScope::IS_VISIBLE_FALSE,
                ],
                'form' => [
                    'is_enabled' => false
                ],
                'view' => ['is_displayable' => false],
                'merge' => ['display' => false],
                'dataaudit' => ['auditable' => false]
            ]
        );
    }

    /**
     * @param Schema $schema
     */
    protected function createLifetimeFields(Schema $schema)
    {
        $table = $schema->getTable('oro_account');
        $table->addColumn(
            'lifetime',
            'money',
            [
                OroOptions::KEY => [
                    'extend' => [
                        'owner' => ExtendScope::OWNER_CUSTOM,
                        'is_extend' => true,
                    ],
                    'datagrid' => ['is_visible' => false],
                    'form' => ['is_enabled' => false,],
                    'view' => ['is_displayable' => false],
                    'merge' => ['display' => false],
                    'dataaudit' => ['auditable' => true],
                    'importexport' => [
                        'full' => true,
                        'order' => 15
                    ]
                ],
            ]
        );
    }

    /**
     * @param Schema $schema
     */
    protected function createChannelFields(Schema $schema)
    {
        $this->extendExtension->addManyToOneRelation(
            $schema,
            'oro_account',
            'dataChannel',
            'orocrm_channel',
            'name',
            [
                ExtendOptionsManager::MODE_OPTION => ConfigModel::MODE_READONLY,
                'extend' => [
                    'owner' => ExtendScope::OWNER_CUSTOM,
                    'is_extend' => true,
                ],
                'form' => [
                    'is_enabled' => false
                ],
                'datagrid' => [
                    'is_visible' => DatagridScope::IS_VISIBLE_FALSE
                ],
                'view' => ['is_displayable' => false],
                'merge' => ['display' => false],
                'dataaudit' => ['auditable' => false]
            ]
        );
    }
}
