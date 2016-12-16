<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\ActivityListBundle\Migration\Extension\ActivityListExtension;
use Oro\Bundle\ActivityListBundle\Migration\Extension\ActivityListExtensionAwareInterface;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
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
        return 'v1_1';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        if ($schema->hasTable('oro_account') && $schema->hasTable('orocrm_account')) {
            $this->createFields($schema);
            $this->addInheritanceTargets($schema);
        }
    }

    /**
     * @param Schema $schema
     */
    public function addInheritanceTargets(Schema $schema)
    {
        $customerPath = [
            'join'          => 'Oro\Bundle\SalesBundle\Entity\Customer',
            'conditionType' => 'WITH',
            'field'         => AccountCustomerManager::getCustomerTargetField(
                'Oro\Bundle\CustomerBundle\Entity\Account'
            ),
        ];

        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_account',
            [$customerPath, 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_order',
            ['account', $customerPath, 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_account_user',
            ['account', $customerPath, 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_shopping_list',
            ['account', $customerPath, 'account']
        );

        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_sale_quote',
            ['account', $customerPath, 'account']
        );
        $this->activityListExtension->addInheritanceTargets(
            $schema,
            'orocrm_account',
            'oro_rfp_request',
            ['account', $customerPath, 'account']
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
}
