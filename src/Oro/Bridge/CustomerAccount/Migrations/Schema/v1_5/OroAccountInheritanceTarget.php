<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ActivityListBundle\Helper\ActivityInheritanceTargetsHelper;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigEntityValueQuery;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\ConnectionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\ConnectionAwareTrait;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Component\DependencyInjection\ContainerAwareInterface;
use Oro\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Update oro account inheritance target.
 */
class OroAccountInheritanceTarget implements
    Migration,
    ContainerAwareInterface,
    ExtendExtensionAwareInterface,
    ConnectionAwareInterface
{
    use ContainerAwareTrait;
    use ExtendExtensionAwareTrait;
    use ConnectionAwareTrait;

    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        $customerPath = [
            'join' => 'Oro\Bundle\SalesBundle\Entity\Customer',
            'conditionType' => 'WITH',
            'field' => AccountCustomerManager::getCustomerTargetField(
                'Oro\Bundle\CustomerBundle\Entity\Customer'
            ),
        ];
        $previousPath = [$customerPath, 'account'];
        $inheritanceTargets = $this->getInheritanceTargetHelper()->getInheritanceTargets(Account::class);
        $inheritanceTargetClass = $this->extendExtension->getEntityClassByTableName('oro_order');
        $isUpdateNeeded = false;
        foreach ($inheritanceTargets as $key => $inheritanceTarget) {
            // compare if exists inheritance target with expected path
            if ($inheritanceTarget['target'] !== $inheritanceTargetClass
                || strcmp(json_encode($inheritanceTarget['path']), json_encode($previousPath)) === 1
            ) {
                continue;
            }
            // replace target with updated path
            $inheritanceTargets[$key] = [
                'target' => $inheritanceTargetClass,
                'path' => ['customer', $customerPath, 'account']
            ];
            $isUpdateNeeded = true;
        }
        if (!$isUpdateNeeded) {
            return;
        }

        $this->updateViewsOnPageParameter();

        $queries->addPreQuery(
            new UpdateEntityConfigEntityValueQuery(
                Account::class,
                'activity',
                'inheritance_targets',
                $inheritanceTargets
            )
        );
    }

    protected function getInheritanceTargetHelper(): ?ActivityInheritanceTargetsHelper
    {
        return $this->container->get('oro_activity_list.helper.activity_inheritance_targets');
    }

    private function updateViewsOnPageParameter(): void
    {
        $configData = $this->loadConfigData();
        $this->processConfigData($configData);
    }

    private function loadConfigData(): array
    {
        $sql = sprintf(
            'SELECT %s, %s FROM %s',
            'id',
            'data',
            'oro_entity_config'
        );

        $configValues = $this->connection->fetchAllAssociative($sql);

        $configData = [];
        foreach ($configValues as $configValue) {
            $configData[$configValue['id']] = $configValue['data'];
        }

        return $configData;
    }

    private function processConfigData(array $configData): void
    {
        foreach ($configData as $id => $data) {
            $data = $this->connection->convertToPHPValue($data, Types::ARRAY);
            if (!empty($data['activity']['show_on_page']) && is_string($data['activity']['show_on_page'])) {
                $data['activity']['show_on_page'] = constant($data['activity']['show_on_page']);
                $this->saveConfigData($id, $data);
            }
        }
    }

    private function saveConfigData(int $id, array $data): void
    {
        $sql = sprintf('UPDATE %s SET data = :data WHERE id = :id', 'oro_entity_config');

        $parameters = [
            'data' => $data,
            'id' => $id,
        ];
        $types = ['id' => Types::INTEGER, 'data' => Types::ARRAY];
        $this->connection->executeStatement($sql, $parameters, $types);
    }
}
