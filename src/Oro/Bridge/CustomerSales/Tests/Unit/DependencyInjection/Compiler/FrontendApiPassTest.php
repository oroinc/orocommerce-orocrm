<?php

namespace Oro\Bridge\CustomerSales\Tests\Unit\DependencyInjection\Compiler;

use Oro\Bridge\CustomerSales\DependencyInjection\Compiler\FrontendApiPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FrontendApiPassTest extends \PHPUnit\Framework\TestCase
{
    private const PROCESSORS = [
        'oro_sales.api.get_config.add_account_customer_associations',
        'oro_sales.api.get_config.add_account_customer_association_descriptions',
        'oro_sales.api.collect_subresources.exclude_change_customer_subresources',
        'oro_sales.api.handle_customer_account_association',
        'oro_sales.api.lead.handle_customer_association',
        'oro_sales.api.opportunity.handle_customer_association',
    ];

    public function testProcessWhenAllProcessorsExist(): void
    {
        $container = new ContainerBuilder();
        $definitions = [];
        foreach (self::PROCESSORS as $serviceId) {
            $definitions[] = $container->register($serviceId)->addTag('oro.api.processor');
        }

        $compiler = new FrontendApiPass();
        $compiler->process($container);

        /** @var Definition $definition */
        foreach ($definitions as $definition) {
            self::assertEquals([['requestType' => '!frontend']], $definition->getTag('oro.api.processor'));
        }
    }

    /**
     * @dataProvider processorsDataProvider
     */
    public function testProcessWhenSomeProcessorDoesNotExist(string $processorServiceId): void
    {
        $container = new ContainerBuilder();
        foreach (self::PROCESSORS as $serviceId) {
            if ($serviceId !== $processorServiceId) {
                $container->register($serviceId)->addTag('oro.api.processor');
            }
        }

        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage(sprintf('non-existent service "%s"', $processorServiceId));

        $compiler = new FrontendApiPass();
        $compiler->process($container);
    }

    public function processorsDataProvider(): array
    {
        return array_map(
            function ($serviceId) {
                return [$serviceId];
            },
            self::PROCESSORS
        );
    }
}
