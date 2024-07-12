<?php

namespace Oro\Bridge\ContactUs\Controller;

use Oro\Bridge\ContactUs\Form\Type\ContactRequestType;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\LayoutBundle\Attribute\Layout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Storefront controller that displays "Contact Us" page, displays and handles "Contact Us" form submission.
 * @see \Oro\Bundle\ContactUsBundle\Entity\ContactRequest form type used on "Contact Us"
 */
class ContactRequestController extends AbstractController
{
    /**
     *
     * @param Request $request
     * @return RedirectResponse
     */
    #[Route(path: '/create', name: 'oro_contactus_bridge_request_create', methods: ['POST'])]
    public function createAction(Request $request)
    {
        $contactRequest = new ContactRequest();
        $form = $this->createForm(
            ContactRequestType::class,
            $contactRequest,
            ['action' => $this->generateUrl('oro_contactus_bridge_request_create')]
        );
        $this->handleForm($form, $contactRequest);

        return $this->redirect($this->getRedirectUri($request));
    }

    private function getRedirectUri(Request $request): string
    {
        $redirectUrl = $request->query->get('requestUri');
        if (!$redirectUrl) {
            return $this->generateUrl('oro_frontend_root');
        }

        $redirectUrlParts = parse_url($redirectUrl);
        // Only URI is allowed, if URL is passed - return oro_frontend_root URI
        if (!empty($redirectUrlParts['host'])) {
            return $this->generateUrl('oro_frontend_root');
        }

        $redirectUrl = $redirectUrlParts['path'] ?? '/';
        if (isset($redirectUrlParts['query'])) {
            $redirectUrl .= '?' . $redirectUrlParts['query'];
        }

        return $redirectUrl;
    }

    /**
     * @return array|RedirectResponse
     */
    #[Route(path: '/', name: 'oro_contactus_bridge_contact_us_page')]
    #[Layout]
    public function contactUsPageAction()
    {
        $contactRequest = new ContactRequest();
        $form = $this->createForm(ContactRequestType::class, $contactRequest);
        $result = $this->handleForm($form, $contactRequest);
        if ($result instanceof Response) {
            return $result;
        }

        return ['data' => ['contact_us_request_form' => $form->createView()]];
    }

    /**
     * Renders errors using OroContactUsBridge/validation.html.twig template.
     */
    private function renderErrors(FormErrorIterator $errors): string
    {
        return $this->renderView('@OroContactUsBridge/validation.html.twig', ['errors' => $errors]);
    }

    private function handleForm(FormInterface $form, ContactRequest $request): array|RedirectResponse
    {
        $callback = function ($entity, FormInterface $form) {
            $errors = $form->getErrors(true);
            if (count($errors) > 0) {
                $this->addFlash('error', $this->renderErrors($errors));
            }

            return [];
        };

        return $this->container->get(UpdateHandlerFacade::class)->update(
            $request,
            $form,
            $this->container->get(TranslatorInterface::class)->trans('oro.contactus.form.contactrequest.sent'),
            null,
            null,
            $callback
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                UpdateHandlerFacade::class,
            ]
        );
    }
}
