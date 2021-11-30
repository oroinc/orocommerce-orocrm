<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityListBundle\Migration\Extension\ActivityListExtension;
use Oro\Bundle\ActivityListBundle\Migration\Extension\ActivityListExtensionAwareInterface;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityConfigBundle\Entity\ConfigModel;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManager;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

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
        return 'v1_3';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        if ($schema->hasTable('oro_customer') && $schema->hasTable('orocrm_account')) {
            $this->createFields($schema);
            $this->addInheritanceTargets($schema);
            $this->createLifetimeFields($schema);
            $this->createChannelFields($schema);
        }
    }

    public function addInheritanceTargets(Schema $schema)
    {
        $customerPath = [
            'join'          => 'Oro\Bundle\SalesBundle\Entity\Customer',
            'conditionType' => 'WITH',
            'field'         => AccountCustomerManager::getCustomerTargetField(
                'Oro\Bundle\CustomerBundle\Entity\Customer'
            ),
        ];

        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_customer',
            [$customerPath, 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_order',
            [$customerPath, 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_customer_user',
            ['customer', $customerPath, 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_shopping_list',
            ['customer', $customerPath, 'account']
        );

        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_sale_quote',
            ['customer', $customerPath, 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_rfp_request',
            ['customer', $customerPath, 'account']
        );
    }

    protected function createFields(Schema $schema)
    {
        /**
         * The previous_account association is used to remember customer's account during changing of
         * "Creation New Account" option at System Configuration / Integrations / CRM and Commerce
         * from "For each Commerce Customer" to "Only for root Commerce Customer" and to restore the account
         * when this option is switched from "Only for root Commerce Customer" to "For each Commerce Customer".
         */
        $this->extendExtension->addManyToOneRelation(
            $schema,
            'oro_customer',
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
                'dataaudit' => ['auditable' => false],
                'importexport' => ['excluded' => true],
            ]
        );
    }

    protected function createLifetimeFields(Schema $schema)
    {
        $table = $schema->getTable('oro_customer');
        $table->addColumn(
            'lifetime',
            'money',
            [
                OroOptions::KEY => [
                    'extend' => [
                        'owner' => ExtendScope::OWNER_CUSTOM,
                        'is_extend' => true,
                    ],
                    'datagrid' => ['is_visible' => DatagridScope::IS_VISIBLE_FALSE],
                    'form' => ['is_enabled' => false,],
                    'view' => ['is_displayable' => false],
                    'merge' => ['display' => false],
                    'dataaudit' => ['auditable' => true],
                    'importexport' => [
                        'excluded' => true,
                    ]
                ],
            ]
        );
    }

    protected function createChannelFields(Schema $schema)
    {
        $this->extendExtension->addManyToOneRelation(
            $schema,
            'oro_customer',
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
                'dataaudit' => ['auditable' => false],
                'importexport' => ['excluded' => true],
            ]
        );
    }
}
