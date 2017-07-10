<?php

namespace Oro\Bridge\CustomerSales;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Oro\Bridge\CustomerSales\DependencyInjection\Compiler\CustomerAssignHelperPass;

class OroCustomerSalesBridgeBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CustomerAssignHelperPass());
    }
}
