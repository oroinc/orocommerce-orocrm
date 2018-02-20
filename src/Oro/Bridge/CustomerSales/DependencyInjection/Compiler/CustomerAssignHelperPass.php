<?php

namespace Oro\Bridge\CustomerSales\DependencyInjection\Compiler;

use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\SalesBundle\Entity\Customer as SalesCustomer;
use Oro\Bundle\SalesBundle\EntityConfig\CustomerScope;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomerAssignHelperPass implements CompilerPassInterface
{
    const HELPER_SERVICE_NAME = 'oro_customer.customer.assign.helper';

    /**
     * {@inheritdoc}
     */
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
