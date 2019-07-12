<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\EventListener;

use Oro\Bridge\CustomerAccount\EventListener\AccountViewListener;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Bundle\UIBundle\View\ScrollData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class CustomerViewListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $translator;

    /** @var DoctrineHelper|\PHPUnit\Framework\MockObject\MockObject */
    protected $doctrineHelper;

    /** @var Environment|\PHPUnit\Framework\MockObject\MockObject */
    protected $env;

    /** @var AccountViewListener */
    protected $listener;

    /** @var Request|\PHPUnit\Framework\MockObject\MockObject */
    protected $request;

    /** @var ConfigManager|\PHPUnit\Framework\MockObject\MockObject */
    protected $configManager;

    /** @var RequestStack|\PHPUnit\Framework\MockObject\MockObject */
    protected $requestStack;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->expects($this->any())
            ->method('trans')
            ->willReturnCallback(
                function ($id) {
                    return $id . '.trans';
                }
            );

        $this->env = $this->createMock(Environment::class);
        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);

        $this->request = $this->createMock(Request::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->configManager = $this->createMock(ConfigManager::class);

        $this->listener =
            new AccountViewListener(
                Account::class,
                $this->doctrineHelper,
                $this->requestStack,
                $this->translator,
                $this->configManager
            );
    }

    public function testOnViewWithEmptyRequest()
    {
        $event = new BeforeListRenderEvent($this->env, new ScrollData(), new \stdClass());

        $this->requestStack->expects(self::any())->method('getCurrentRequest')->willReturn(null);
        $this->env->expects(self::never())->method('render');

        $this->listener->onView($event);
    }

    public function testOnView()
    {
        $this->request->expects(self::any())->method('get')->with('id')->willReturn(1);
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->request);

        $account = new Account();

        $this->doctrineHelper
            ->expects(self::once())
            ->method('getEntityReference')
            ->with(Account::class, 1)
            ->willReturn($account);

        $this->env->expects(self::once())
            ->method('render')
            ->with('OroCustomerAccountBridgeBundle:Customer:view.html.twig', ['entity' => $account])
            ->willReturn('');

        $event = new BeforeListRenderEvent(
            $this->env,
            new ScrollData(),
            $account
        );

        $this->listener->onView($event);
    }

    public function testOnViewWithoutId()
    {
        $this->request->expects(self::any())->method('get')->with('id')->willReturn(null);
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->request);

        $this->doctrineHelper
            ->expects(self::never())
            ->method('getEntityReference');

        $event = new BeforeListRenderEvent($this->env, new ScrollData(), new \stdClass());

        $this->listener->onView($event);
    }

    public function testOnViewWithoutEntity()
    {
        $this->request->expects(self::any())->method('get')->with('id')->willReturn(1);
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->request);

        $this->doctrineHelper
            ->expects(self::once())
            ->method('getEntityReference')
            ->with(Account::class, 1)
            ->willReturn(null);

        $this->env->expects(self::never())
            ->method('render');

        $event = new BeforeListRenderEvent($this->env, new ScrollData(), new \stdClass());

        $this->listener->onView($event);
    }
}
