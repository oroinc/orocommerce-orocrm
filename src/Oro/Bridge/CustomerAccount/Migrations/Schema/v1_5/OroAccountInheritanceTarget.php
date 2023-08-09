<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ActivityListBundle\Helper\ActivityInheritanceTargetsHelper;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigEntityValueQuery;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Update oro account inheritance target.
 */
class OroAccountInheritanceTarget implements Migration, ContainerAwareInterface, ExtendExtensionAwareInterface
{
    use ContainerAwareTrait;

    /** @var ExtendExtension */
    protected $extendExtension;

    public function setExtendExtension(ExtendExtension $extendExtension): void
    {
        $this->extendExtension = $extendExtension;
    }

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
                || strcmp(json_encode($inheritanceTarget['path']), json_encode($previousPath)) === 1) {
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
}
