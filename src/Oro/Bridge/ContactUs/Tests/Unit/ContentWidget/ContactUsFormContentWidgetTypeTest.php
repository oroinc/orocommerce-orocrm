<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\ContentWidget;

use Oro\Bridge\ContactUs\ContentWidget\ContactUsFormContentWidgetType;
use Oro\Bridge\ContactUs\Form\Type\ContactRequestType;
use Oro\Bundle\CMSBundle\Entity\ContentWidget;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class ContactUsFormContentWidgetTypeTest extends \PHPUnit\Framework\TestCase
{
    /** @var FormFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $formFactory;

    /** @var UrlGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $urlGenerator;

    /** @var RequestStack|\PHPUnit\Framework\MockObject\MockObject */
    private $requestStack;

    /** @var ContactUsFormContentWidgetType */
    private $contentWidgetType;

    protected function setUp(): void
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->contentWidgetType = new ContactUsFormContentWidgetType(
            $this->formFactory,
            $this->urlGenerator,
            $this->requestStack
        );
    }

    public function testGetName(): void
    {
        $this->assertEquals('contact_us_form', $this->contentWidgetType::getName());
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('oro.contactus.content_widget.contact_us_form.label', $this->contentWidgetType->getLabel());
    }

    public function testIsInline(): void
    {
        $this->assertFalse($this->contentWidgetType->isInline());
    }

    public function testGetWidgetData(): void
    {
        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request = $this->createMock(Request::class));

        $request
            ->method('getRequestUri')
            ->willReturn($requestUri = 'sample/uri');

        $this->urlGenerator
            ->method('generate')
            ->with('oro_contactus_bridge_request_create', ['requestUri' => $requestUri])
            ->willReturn($url = 'sample/url');

        $this->formFactory
            ->method('create')
            ->with(ContactRequestType::class, new ContactRequest, ['action' => $url])
            ->willReturn($form = $this->createMock(FormInterface::class));

        $form
            ->method('createView')
            ->willReturn($formView = $this->createMock(FormView::class));

        $this->assertEquals(['form' => $formView], $this->contentWidgetType->getWidgetData(new ContentWidget()));
    }

    public function testGetSettingsForm(): void
    {
        $this->assertNull($this->contentWidgetType->getSettingsForm(new ContentWidget(), Forms::createFormFactory()));
    }

    public function testGetBackOfficeViewSubBlocks(): void
    {
        $twig = $this->createMock(Environment::class);

        $this->assertEquals(
            [],
            $this->contentWidgetType->getBackOfficeViewSubBlocks(new ContentWidget(), $twig)
        );
    }

    public function testGetDefaultTemplate(): void
    {
        $contentWidget = new ContentWidget();
        $contentWidget->setSettings(['param' => 'value']);

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request = $this->createMock(Request::class));

        $request
            ->method('getRequestUri')
            ->willReturn($requestUri = 'sample/uri');

        $this->urlGenerator
            ->method('generate')
            ->with('oro_contactus_bridge_request_create', ['requestUri' => $requestUri])
            ->willReturn($url = 'sample/url');

        $this->formFactory
            ->method('create')
            ->with(ContactRequestType::class, new ContactRequest, ['action' => $url])
            ->willReturn($form = $this->createMock(FormInterface::class));

        $form
            ->method('createView')
            ->willReturn($formView = $this->createMock(FormView::class));

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('@OroContactUsBridge/ContactUsFormContentWidget/widget.html.twig', ['form' => $formView])
            ->willReturn('rendered template');

        $this->assertEquals('rendered template', $this->contentWidgetType->getDefaultTemplate($contentWidget, $twig));
    }
}
