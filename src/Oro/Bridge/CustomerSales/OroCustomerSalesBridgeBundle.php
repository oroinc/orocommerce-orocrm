<?php

namespace Oro\Bridge\CustomerSales;

use Oro\Bridge\CustomerSales\DependencyInjection\Compiler\CustomerAssignHelperPass;
use Oro\Bridge\CustomerSales\DependencyInjection\Compiler\FrontendApiPass;
use Oro\Bundle\ApiBundle\DependencyInjection\Compiler\ProcessorBagCompilerPass;
use Oro\Component\DependencyInjection\ExtendedContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OroCustomerSalesBridgeBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CustomerAssignHelperPass());
        if ($container instanceof ExtendedContainerBuilder) {
            $container->addCompilerPass(new FrontendApiPass());
            $container->moveCompilerPassBefore(FrontendApiPass::class, ProcessorBagCompilerPass::class);
        }
    }
}
