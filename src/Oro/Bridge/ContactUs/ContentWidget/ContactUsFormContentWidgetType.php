<?php

namespace Oro\Bridge\ContactUs\ContentWidget;

use Oro\Bridge\ContactUs\Form\Type\ContactRequestType;
use Oro\Bundle\CMSBundle\ContentWidget\AbstractContentWidgetType;
use Oro\Bundle\CMSBundle\Entity\ContentWidget;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Content widget type for Contact Us form.
 */
class ContactUsFormContentWidgetType extends AbstractContentWidgetType
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private UrlGeneratorInterface $urlGenerator,
        private RequestStack $requestStack
    ) {
    }

    #[\Override]
    public static function getName(): string
    {
        return 'contact_us_form';
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'oro.contactus.content_widget.contact_us_form.label';
    }

    #[\Override]
    public function getWidgetData(ContentWidget $contentWidget): array
    {
        $form = $this->formFactory->create(
            ContactRequestType::class,
            new ContactRequest(),
            [
                'action' => $this->urlGenerator->generate(
                    'oro_contactus_bridge_request_create',
                    ['requestUri' => $this->requestStack->getCurrentRequest()?->getRequestUri()]
                ),
            ]
        );

        return ['form' => $form->createView()];
    }

    #[\Override]
    public function getDefaultTemplate(ContentWidget $contentWidget, Environment $twig): string
    {
        return $twig->render(
            '@OroContactUsBridge/ContactUsFormContentWidget/widget.html.twig',
            $this->getWidgetData($contentWidget)
        );
    }
}
