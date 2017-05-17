<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\Twig;

use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bridge\ContactUs\Form\Type\ContactRequestType;
use Oro\Bridge\ContactUs\Widget\ContactRequestFormWidget;

class ContactRequestFormWidgetTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContactRequestFormWidget */
    private $widget;

    /** @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $urlGenerator;

    /** @var FormFactoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $formFactory;

    /** @var TwigRenderer|\PHPUnit_Framework_MockObject_MockObject */
    private $twigRenderer;

    /** @var RequestStack|\PHPUnit_Framework_MockObject_MockObject */
    private $requestStack;

    protected function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->twigRenderer = $this->createMock(TwigRenderer::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->widget = new ContactRequestFormWidget(
            $this->formFactory,
            $this->urlGenerator,
            $this->twigRenderer,
            $this->requestStack
        );
    }

    public function testContactUsFormFunctionCall()
    {
        $formView = $this->createMock(FormView::class);
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $action = 'form/action';
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->willReturn($action);
        $this->formFactory->expects($this->once())
            ->method('create')
            ->with(
                ContactRequestType::class,
                new ContactRequest,
                [
                    'action' => $action,
                ]
            )
            ->willReturn($form);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getRequestUri')
            ->willReturn('request_uri');

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $renderedForm = '<form>Rendered form</form>';
        $this->twigRenderer->expects($this->once())
            ->method('searchAndRenderBlock')
            ->with($formView, 'widget')
            ->willReturn($renderedForm);

        $this->assertEquals($renderedForm, $this->widget->render());
    }
}
