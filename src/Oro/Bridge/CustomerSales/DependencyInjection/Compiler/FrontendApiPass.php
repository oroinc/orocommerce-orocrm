<?php

namespace Oro\Bridge\CustomerSales\DependencyInjection\Compiler;

use Oro\Bundle\FrontendBundle\Api\FrontendApiDependencyInjectionUtil;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Configures frontend API processors.
 */
class FrontendApiPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $processorsToBeDisabled = [
            'oro_sales.api.get_config.add_account_customer_associations',
            'oro_sales.api.get_config.add_account_customer_association_descriptions',
            'oro_sales.api.collect_subresources.exclude_change_customer_subresources',
            'oro_sales.api.handle_customer_account_association',
            'oro_sales.api.lead.handle_customer_association',
            'oro_sales.api.opportunity.handle_customer_association',
        ];
        foreach ($processorsToBeDisabled as $serviceId) {
            FrontendApiDependencyInjectionUtil::disableProcessorForFrontendApi($container, $serviceId);
        }
    }
}
