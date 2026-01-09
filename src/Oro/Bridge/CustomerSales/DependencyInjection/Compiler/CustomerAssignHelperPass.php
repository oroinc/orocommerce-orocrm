<?php

namespace Oro\Bridge\CustomerSales\DependencyInjection\Compiler;

use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\SalesBundle\Entity\Customer as SalesCustomer;
use Oro\Bundle\SalesBundle\EntityConfig\CustomerScope;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass that configures the customer assignment helper service.
 *
 * Registers ignored relations in the customer assignment helper to prevent the
 * sales customer association from being processed during customer assignment operations.
 * This ensures that the customer-to-account relationship is managed separately from
 * other customer relations.
 */
class CustomerAssignHelperPass implements CompilerPassInterface
{
    public const HELPER_SERVICE_NAME = 'oro_customer.customer.assign.helper';

    #[\Override]
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::HELPER_SERVICE_NAME)) {
            return;
        }

        $associationName = ExtendHelper::buildAssociationName(
            Customer::class,
            CustomerScope::ASSOCIATION_KIND
        );

        $container->getDefinition(self::HELPER_SERVICE_NAME)
            ->addMethodCall('addIgnoredRelation', [SalesCustomer::class, $associationName]);
    }
}
