<?php

namespace Oro\Bundle\DemoDataCommerceCRMBundle\Tests\Unit\EventListener;

use Oro\Bundle\DemoDataCommerceCRMBundle\EventListener\DataFixturesCommandListener;
use Oro\Bundle\MigrationBundle\Command\LoadDataFixturesCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class DataFixturesCommandListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var DataFixturesCommandListener */
    private $listener;

    protected function setUp(): void
    {
        $this->listener = new DataFixturesCommandListener();
    }

    public function testOnConsoleCommand(): void
    {
        $option = $this->createMock(InputOption::class);
        $option->expects($this->once())
            ->method('getDefault')
            ->willReturn(['OroTestBundle']);
        $option->expects($this->once())
            ->method('setDefault')
            ->willReturn(['OroTestBundle', 'OroMagentoBundle', 'OroMarketingCRMBridgeBundle']);

        $definition = $this->createMock(InputDefinition::class);
        $definition->expects($this->once())
            ->method('hasOption')
            ->with('exclude')
            ->willReturn(true);
        $definition->expects($this->once())
            ->method('getOption')
            ->with('exclude')
            ->willReturn($option);

        $command = $this->createMock(Command::class);
        $command->expects($this->once())
            ->method('getName')
            ->willReturn(LoadDataFixturesCommand::getDefaultName());
        $command->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        $input = $this->createMock(InputInterface::class);
        $input->expects($this->once())
            ->method('getOption')
            ->with('fixtures-type')
            ->willReturn(LoadDataFixturesCommand::DEMO_FIXTURES_TYPE);

        $this->listener->onConsoleCommand($this->getEvent($command, $input));
    }

    public function testOnConsoleCommandNoCommand(): void
    {
        $this->listener->onConsoleCommand($this->getEvent());
    }

    public function testOnConsoleCommandIncorrectCommandName(): void
    {
        $command = $this->createMock(Command::class);
        $command->expects($this->once())
            ->method('getName')
            ->willReturn('test_name');
        $command->expects($this->never())
            ->method('getDefinition');

        $this->listener->onConsoleCommand($this->getEvent($command));
    }

    public function testOnConsoleCommandIncorrectFixturesType(): void
    {
        $command = $this->createMock(Command::class);
        $command->expects($this->once())
            ->method('getName')
            ->willReturn(LoadDataFixturesCommand::getDefaultName());
        $command->expects($this->never())
            ->method('getDefinition');

        $input = $this->createMock(InputInterface::class);
        $input->expects($this->once())
            ->method('getOption')
            ->with('fixtures-type')
            ->willReturn('test_type');

        $this->listener->onConsoleCommand($this->getEvent($command, $input));
    }

    public function testOnConsoleCommandNoExcludeOption(): void
    {
        $definition = $this->createMock(InputDefinition::class);
        $definition->expects($this->once())
            ->method('hasOption')
            ->with('exclude')
            ->willReturn(false);
        $definition->expects($this->never())
            ->method('getOption');

        $command = $this->createMock(Command::class);
        $command->expects($this->once())
            ->method('getName')
            ->willReturn(LoadDataFixturesCommand::getDefaultName());
        $command->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        $input = $this->createMock(InputInterface::class);
        $input->expects($this->once())
            ->method('getOption')
            ->with('fixtures-type')
            ->willReturn(LoadDataFixturesCommand::DEMO_FIXTURES_TYPE);

        $this->listener->onConsoleCommand($this->getEvent($command, $input));
    }

    /**
     * @param \PHPUnit\Framework\MockObject\MockObject|Command|null $command
     * @param \PHPUnit\Framework\MockObject\MockObject|InputInterface|null $input
     * @return \PHPUnit\Framework\MockObject\MockObject|ConsoleCommandEvent
     */
    private function getEvent(Command $command = null, InputInterface $input = null): ConsoleCommandEvent
    {
        /** @var ConsoleCommandEvent|\PHPUnit\Framework\MockObject\MockObject $event */
        $event = $this->createMock(ConsoleCommandEvent::class);
        $event->expects($this->once())
            ->method('getCommand')
            ->willReturn($command);
        $event->expects($input ? $this->once() : $this->never())
            ->method('getInput')
            ->willReturn($input);

        return $event;
    }
}
