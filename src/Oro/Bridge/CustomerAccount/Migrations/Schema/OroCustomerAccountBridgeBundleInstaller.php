<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityListBundle\Migration\Extension\ActivityListExtensionAwareInterface;
use Oro\Bundle\ActivityListBundle\Migration\Extension\ActivityListExtensionAwareTrait;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityConfigBundle\Entity\ConfigModel;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManager;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareTrait;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

class OroCustomerAccountBridgeBundleInstaller implements
    Installation,
    ExtendExtensionAwareInterface,
    ActivityListExtensionAwareInterface
{
    use ExtendExtensionAwareTrait;
    use ActivityListExtensionAwareTrait;

    #[\Override]
    public function getMigrationVersion(): string
    {
        return 'v1_5';
    }

    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        if ($schema->hasTable('oro_customer') && $schema->hasTable('orocrm_account')) {
            $this->createFields($schema);
            $this->addInheritanceTargets($schema);
            $this->createLifetimeFields($schema);
            $this->createChannelFields($schema);
        }
    }

    private function addInheritanceTargets(Schema $schema): void
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
            ['customer', $customerPath, 'account']
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

    private function createFields(Schema $schema): void
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

    private function createLifetimeFields(Schema $schema): void
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
                    'dataaudit' => ['auditable' => false],
                    'importexport' => [
                        'excluded' => true,
                    ]
                ],
            ]
        );
    }

    private function createChannelFields(Schema $schema): void
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
