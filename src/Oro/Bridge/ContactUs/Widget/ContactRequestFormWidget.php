<?php

namespace Oro\Bridge\ContactUs\Widget;

use Oro\Bridge\ContactUs\Form\Type\ContactRequestType;
use Oro\Bundle\CMSBundle\Widget\WidgetInterface;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContactRequestFormWidget implements WidgetInterface
{
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var FormRendererInterface */
    protected $twigRenderer;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * @param FormFactoryInterface  $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     * @param FormRendererInterface $twigRenderer
     * @param RequestStack          $requestStack
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        FormRendererInterface $twigRenderer,
        RequestStack $requestStack
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->twigRenderer = $twigRenderer;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $options = [])
    {
        $form = $this->formFactory->create(
            ContactRequestType::class,
            new ContactRequest,
            [
                'action' => $this->urlGenerator->generate(
                    'oro_contactus_bridge_request_create',
                    ['requestUri' => $this->requestStack->getCurrentRequest()->getRequestUri()]
                ),
            ]
        );

        $formView = $form->createView();

        return $this->twigRenderer->searchAndRenderBlock($formView, 'widget');
    }
}
