<?php
namespace Oro\Bridge\CustomerAccount\Tests\Unit\Async;

use Psr\Log\LoggerInterface;

use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Bridge\CustomerAccount\Manager\AccountManager;
use Oro\Bridge\CustomerAccount\Async\ReassingCustomerProcessor;
use Oro\Bridge\CustomerAccount\Async\Topics;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Transport\Null\NullMessage;
use Oro\Component\MessageQueue\Transport\SessionInterface;

class ReassingCustomerProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldRejectMessageIfTypePropertyIsNotSet()
    {
        $logger = $this->createLoggerMock();
        $logger
            ->expects($this->once())
            ->method('critical')
            ->with('Got invalid message');

        $processor = new ReassingCustomerProcessor(
            $this->createAccountManagerMock(),
            $this->createJobRunnerMock(),
            $logger
        );

        $message = new NullMessage();
        $message->setBody(json_encode([
            'test' => true,
        ]));

        $result = $processor->process($message, $this->createSessionMock());

        $this->assertEquals(MessageProcessorInterface::REJECT, $result);
    }

    public function testShouldSetRunUniqueJobIfTypeIsset()
    {
        $jobRunner = $this->createJobRunnerMock();
        $jobRunner
            ->expects($this->once())
            ->method('runUnique')
            ->with('message-id', Topics::REASSIGN_CUSTOMER_ACCOUNT)
            ->will($this->returnCallback(function ($ownerId, $name, $callback) use ($jobRunner) {
                $callback($jobRunner);

                return true;
            }));

        $message = new NullMessage();
        $message->setMessageId('message-id');
        $message->setBody(json_encode(['type' => 'each']));

        $processor = new ReassingCustomerProcessor(
            $this->createAccountManagerMock(),
            $jobRunner,
            $this->createLoggerMock()
        );
        $result = $processor->process($message, $this->createMock(SessionInterface::class));

        $this->assertEquals(MessageProcessorInterface::ACK, $result);
    }

    public function testShouldReturnSubscribedTopics()
    {
        $this->assertEquals([Topics::REASSIGN_CUSTOMER_ACCOUNT], ReassingCustomerProcessor::getSubscribedTopics());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SessionInterface
     */
    private function createSessionMock()
    {
        return $this->createMock(SessionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private function createLoggerMock()
    {
        return $this->createMock(LoggerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AccountManager
     */
    private function createAccountManagerMock()
    {
        return $this->createMock(AccountManager::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|JobRunner
     */
    private function createJobRunnerMock()
    {
        return $this->createMock(JobRunner::class);
    }
}
