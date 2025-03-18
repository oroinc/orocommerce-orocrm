<?php

namespace Oro\Bridge\ContactUs\Tests\Functional;

use Oro\Bundle\FrontendTestFrameworkBundle\Test\FrontendWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCsrfProtectionOfContactUsFormTest extends FrontendWebTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->initClient();
        $this->client->followRedirects();
    }

    public function testSendContactUsFormWithWrongCsrfToken(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->getUrl('oro_contactus_bridge_contact_us_page'));

        $form = $crawler->filter(\sprintf('form[name=%s]', 'contact_request'))->form();
        $values = $form->getPhpValues();
        $values['contact_request']['firstName'] = 'test';
        $values['contact_request']['lastName'] = 'test';
        $values['contact_request']['comment'] = 'test comment';
        $values['contact_request']['_token'] = '';

        $this->client->request(Request::METHOD_POST, $form->getUri(), $values);

        $result = $this->client->getResponse();

        self::assertSame($result->getStatusCode(), Response::HTTP_OK);
        self::assertStringContainsString(
            'The CSRF token is invalid. Please try to resubmit the form.',
            $result->getContent()
        );
    }
}
