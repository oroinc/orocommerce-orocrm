<?php
namespace Oro\Bridge\QuoteSales\Tests\Functional\Listener;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\TranslationBundle\Translation\Translator;

/**
 * @dbIsolationPerTest
 */
class OpportunityQuotesListenerTest extends WebTestCase
{
    /**
     * @var Translator
     */
    protected $translator;

    public function setUp()
    {
        $this->initClient(
            ['debug' => false],
            array_merge($this->generateBasicAuthHeader(), array('HTTP_X-CSRF-Header' => 1))
        );
        $this->loadFixtures([
            'Oro\Bridge\QuoteSales\Tests\Functional\Fixture\CreateDefaultAccountFixture',
            'Oro\Bridge\QuoteSales\Tests\Functional\Fixture\OpportunityQuotesListenerFixture'
        ]);
        $this->translator = static::getContainer()->get('translator');
    }

    public function testQuoteGridOnOpportunityView()
    {
        $opportunity = $this->getReference('opportunity');
        $crawler = $this->client->request(
            'GET',
            $this->getUrl(
                'oro_sales_opportunity_view',
                [
                    'id' => $opportunity->getId(),

                ]
            )
        );
        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), 200, "Failed in getting widget view !");
        $this->assertNotEmpty($crawler->html());
        $this->assertContains(
            $this->translator->trans('oro.sale.quote.entity_plural_label'),
            $crawler->html()
        );
    }
}
