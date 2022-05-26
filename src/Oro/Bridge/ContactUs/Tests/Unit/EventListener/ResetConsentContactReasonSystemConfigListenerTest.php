<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\EventListener;

use Oro\Bridge\ContactUs\EventListener\ResetConsentContactReasonSystemConfigListener;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ContactUsBundle\Entity\ContactReason;
use Oro\Bundle\ContactUsBundle\Tests\Unit\Stub\ContactReasonStub;
use Oro\Component\Testing\ReflectionUtil;

class ResetConsentContactReasonSystemConfigListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ConfigManager|\PHPUnit\Framework\MockObject\MockObject */
    private $configManager;

    /** @var ResetConsentContactReasonSystemConfigListener */
    private $listener;

    protected function setUp(): void
    {
        $this->configManager = $this->createMock(ConfigManager::class);

        $this->listener = new ResetConsentContactReasonSystemConfigListener($this->configManager);
    }

    private function getContactReason(int $id): ContactReason
    {
        $contactReason = new ContactReasonStub();
        ReflectionUtil::setId($contactReason, $id);

        return $contactReason;
    }

    public function testOnPreRemoveInvalid(): void
    {
        $id = 42;
        $contactReason = $this->getContactReason(44);

        $this->configManager->expects(self::once())
            ->method('get')
            ->with('oro_contact_us_bridge.consent_contact_reason')
            ->willReturn($id);
        $this->configManager->expects(self::never())
            ->method('reset');
        $this->configManager->expects(self::never())
            ->method('flush');

        $this->listener->onPreRemove($contactReason);
    }

    public function testOnPreRemove(): void
    {
        $id = 42;
        $contactReason = $this->getContactReason($id);

        $this->configManager->expects(self::once())
            ->method('get')
            ->with('oro_contact_us_bridge.consent_contact_reason')
            ->willReturn($id);
        $this->configManager->expects(self::once())
            ->method('reset')
            ->with('oro_contact_us_bridge.consent_contact_reason');
        $this->configManager->expects(self::once())
            ->method('flush');

        $this->listener->onPreRemove($contactReason);
    }
}
