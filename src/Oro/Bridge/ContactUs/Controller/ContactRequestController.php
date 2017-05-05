<?php

namespace Oro\Bridge\ContactUs\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Oro\Bridge\ContactUs\Form\Type\ContactRequestType;
use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\LayoutBundle\Annotation\Layout;

class ContactRequestController extends Controller
{
    /**
     * @Route(
     *      "/create",
     *      name="oro_contactus_bridge_request_create"
     * )
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function createAction(Request $request)
    {
        $contactRequest = new ContactRequest();

        $form = $this->createForm(
            ContactRequestType::class,
            $contactRequest,
            [
                'action' => $this->generateUrl('oro_contactus_bridge_request_create'),
            ]
        );

        $this->get('oro_form.update_handler')->update(
            $contactRequest,
            $form,
            $this->get('translator')->trans('oro.contactus.form.contactrequest.sent'),
            null,
            null,
            function ($entity, FormInterface $form) {
                $this->addFlash('error', $this->formatErrors($form->getErrors(true)));
                return [];
            }
        );

        return new RedirectResponse(
            $request->query->get(
                'requestUri',
                $request->headers->get('referer')
            )
        );
    }

    /**
     * @Route(
     *      "/show",
     *      name="oro_contactus_bridge_request_show"
     * )
     * @Layout
     *
     * @return array|RedirectResponse
     */
    public function showAction()
    {
        $contactRequest = new ContactRequest();

        $form = $this->createForm(
            ContactRequestType::class,
            $contactRequest,
            [
                'action' => $this->generateUrl(
                    'oro_contactus_bridge_request_create'
                ),
            ]
        );

        $this->get('oro_form.update_handler')->update(
            $contactRequest,
            $form,
            $this->get('translator')->trans('oro.contactus.form.contactrequest.sent'),
            null,
            null,
            function ($entity, FormInterface $form) {
                return ['data' => ['contact_us_request_form' => $form->createView()]];
            }
        );

        return ['data' => ['contact_us_request_form' => $form->createView()]];
    }

    /**
     * @param FormErrorIterator $errors
     * @return string
     */
    private function formatErrors(FormErrorIterator $errors)
    {
        if (count($errors) > 0) {
            return $this->renderView(
                '@OroContactUsBridge/validation.html.twig',
                [
                    'errors' => $errors,
                ]
            );
        }

        return '';
    }
}
