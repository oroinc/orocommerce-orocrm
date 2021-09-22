<?php

namespace Oro\Bundle\DemoDataCommerceCRMBundle\Tests\Unit\EventListener;

use Oro\Bundle\DemoDataCommerceCRMBundle\EventListener\DataFixturesCommandListener;
use Oro\Bundle\MigrationBundle\Command\LoadDataFixturesCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DataFixturesCommandListenerTest extends \PHPUnit\Framework\TestCase
{
    private DataFixturesCommandListener $listener;

    protected function setUp(): void
    {
        $this->listener = new DataFixturesCommandListener();
    }

    public function testOnConsoleCommand(): void
    {
        $option = $this->createMock(InputOption::class);
        $option->expects(self::once())
            ->method('getDefault')
            ->willReturn(['OroTestBundle']);
        $option->expects(self::once())
            ->method('setDefault')
            ->willReturn(['OroTestBundle', 'OroMagentoBundle', 'OroMarketingCRMBridgeBundle']);

        $definition = $this->createMock(InputDefinition::class);
        $definition->expects(self::once())
            ->method('hasOption')
            ->with('exclude')
            ->willReturn(true);
        $definition->expects(self::once())
            ->method('getOption')
            ->with('exclude')
            ->willReturn($option);

        $command = $this->createMock(Command::class);
        $command->expects(self::once())
            ->method('getName')
            ->willReturn(LoadDataFixturesCommand::getDefaultName());
        $command->expects(self::once())
            ->method('getDefinition')
            ->willReturn($definition);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::once())
            ->method('getOption')
            ->with('fixtures-type')
            ->willReturn(LoadDataFixturesCommand::DEMO_FIXTURES_TYPE);

        $this->listener->onConsoleCommand($this->getEvent($command, $input));
    }

    public function testOnConsoleCommandNoCommand(): void
    {
        $this->listener->onConsoleCommand($this->getEvent(null, $this->createMock(InputInterface::class)));
    }

    public function testOnConsoleCommandIncorrectCommandName(): void
    {
        $command = $this->createMock(Command::class);
        $command->expects(self::once())
            ->method('getName')
            ->willReturn('test_name');
        $command->expects(self::never())
            ->method('getDefinition');

        $this->listener->onConsoleCommand($this->getEvent($command, $this->createMock(InputInterface::class)));
    }

    public function testOnConsoleCommandIncorrectFixturesType(): void
    {
        $command = $this->createMock(Command::class);
        $command->expects(self::once())
            ->method('getName')
            ->willReturn(LoadDataFixturesCommand::getDefaultName());
        $command->expects(self::never())
            ->method('getDefinition');

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::once())
            ->method('getOption')
            ->with('fixtures-type')
            ->willReturn('test_type');

        $this->listener->onConsoleCommand($this->getEvent($command, $input));
    }

    public function testOnConsoleCommandNoExcludeOption(): void
    {
        $definition = $this->createMock(InputDefinition::class);
        $definition->expects(self::once())
            ->method('hasOption')
            ->with('exclude')
            ->willReturn(false);
        $definition->expects(self::never())
            ->method('getOption');

        $command = $this->createMock(Command::class);
        $command->expects(self::once())
            ->method('getName')
            ->willReturn(LoadDataFixturesCommand::getDefaultName());
        $command->expects(self::once())
            ->method('getDefinition')
            ->willReturn($definition);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::once())
            ->method('getOption')
            ->with('fixtures-type')
            ->willReturn(LoadDataFixturesCommand::DEMO_FIXTURES_TYPE);

        $this->listener->onConsoleCommand($this->getEvent($command, $input));
    }

    /**
     * @param \PHPUnit\Framework\MockObject\MockObject|Command|null $command
     * @param \PHPUnit\Framework\MockObject\MockObject|InputInterface $input
     * @return ConsoleCommandEvent
     */
    private function getEvent(?Command $command, InputInterface $input): ConsoleCommandEvent
    {
        return new ConsoleCommandEvent($command, $input, $this->createMock(OutputInterface::class));
    }
}
