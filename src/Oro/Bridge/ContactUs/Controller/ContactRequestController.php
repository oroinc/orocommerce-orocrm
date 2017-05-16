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
                $errors = $form->getErrors(true);
                if (count($errors) > 0) {
                    $this->addFlash('error', $this->renderErrors($errors));
                }

                return [];
            }
        );

        return $this->redirect($request->query->get('requestUri', $this->generateUrl('oro_frontend_root')));
    }

    /**
     * @param FormErrorIterator $errors
     * @return string
     */
    private function renderErrors(FormErrorIterator $errors)
    {
        return $this->renderView('@OroContactUsBridge/validation.html.twig', ['errors' => $errors]);
    }
}