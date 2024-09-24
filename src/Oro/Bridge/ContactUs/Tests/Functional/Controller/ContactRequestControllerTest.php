<?php

namespace Oro\Bridge\ContactUs\Tests\Functional\Controller;

use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class ContactRequestControllerTest extends WebTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->initClient();
    }

    /**
     * @dataProvider invalidRedirectUrlDataProvider
     */
    public function testCreateRedirectInvalidUrlMethodGet(string $url)
    {
        $this->client->request(
            'GET',
            $this->getUrl(
                'oro_contactus_bridge_request_create',
                ['requestUri' => $url]
            ),
            ['contact_request' => ['preferredContactMethod' => ContactRequest::CONTACT_METHOD_EMAIL]]
        );

        $result = $this->client->getResponse();
        $this->assertEquals(405, $result->getStatusCode());
    }

    /**
     * @dataProvider invalidRedirectUrlDataProvider
     */
    public function testCreateRedirectInvalidUrlMethodPost(string $url)
    {
        $this->client->request(
            'POST',
            $this->getUrl(
                'oro_contactus_bridge_request_create',
                ['requestUri' => $url]
            ),
            ['contact_request' => ['preferredContactMethod' => ContactRequest::CONTACT_METHOD_EMAIL]]
        );

        $result = $this->client->getResponse();
        $this->assertEquals(302, $result->getStatusCode());
        $this->assertEquals('/', $result->getTargetUrl());
    }

    public static function invalidRedirectUrlDataProvider(): array
    {
        return [
            ['//google.com'],
            ['https://google.com'],
            ['http://google.com'],
            ['http://google.com/contact-us'],
            ['https://google.com/contact-us?search=abc'],
            ['//google.com/contact-us?search=abc'],
        ];
    }

    /**
     * @dataProvider validRedirectUrlDataProvider
     */
    public function testCreateRedirectUrlMethodPost(string $targetUri)
    {
        $this->client->request(
            'POST',
            $this->getUrl(
                'oro_contactus_bridge_request_create',
                ['requestUri' => $targetUri]
            ),
            ['contact_request' => ['preferredContactMethod' => ContactRequest::CONTACT_METHOD_EMAIL]]
        );

        $result = $this->client->getResponse();
        $this->assertEquals(302, $result->getStatusCode());
        $this->assertEquals($targetUri, $result->getTargetUrl());
    }

    public static function validRedirectUrlDataProvider(): array
    {
        return [
            ['/contact-us'],
            ['/contact-us?search=abc'],
        ];
    }
}
