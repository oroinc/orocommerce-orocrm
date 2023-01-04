<?php

namespace Oro\Bridge\ContactUs\Tests\Functional;

use Oro\Bundle\FrontendTestFrameworkBundle\Test\FrontendWebTestCase;

class CheckCsrfProtectionOfContactUsFormTest extends FrontendWebTestCase
{
    protected function setUp(): void
    {
        $this->initClient();
        $this->client->followRedirects();
    }

    public function testSendContactUsFormWithWrongCsrfToken(): void
    {
        $crawler = $this->client->request('GET', $this->getUrl('oro_contactus_bridge_contact_us_page'));

        $form = $crawler->filter(sprintf('form[name=%s]', 'contact_request'))->form();
        $values = $form->getPhpValues();
        $values['contact_request']['firstName'] = 'test';
        $values['contact_request']['lastName'] = 'test';
        $values['contact_request']['comment'] = 'test comment';
        $values['contact_request']['_token'] = '';

        $this->client->request(
            'POST',
            $form->getUri(),
            $values,
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'HTTP_x-oro-hash-navigation' => 'true',
            ]
        );

        $result = $this->client->getResponse();
        self::assertEquals($result->getStatusCode(), 200);
        self::assertStringContainsString(
            'The CSRF token is invalid. Please try to resubmit the form.',
            $result->getContent()
        );
    }
}
