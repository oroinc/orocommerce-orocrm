<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Listener;

use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\CreateDefaultAccountFixture;
use Oro\Bridge\QuoteSales\Tests\Functional\Fixture\OpportunityQuotesListenerFixture;
use Oro\Bundle\DataGridBundle\Tests\Functional\AbstractDatagridTestCase;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;

/**
 * @dbIsolationPerTest
 */
class OpportunityQuotesListenerTest extends AbstractDatagridTestCase
{
    /** @var WorkflowManager */
    private $manager;

    protected function setUp(): void
    {
        $this->initClient(['debug' => false], $this->generateBasicAuthHeader());
        $this->loadFixtures([
            CreateDefaultAccountFixture::class,
            OpportunityQuotesListenerFixture::class
        ]);

        $this->manager = $this->getContainer()->get('oro_workflow.manager');
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
        $this->assertEquals(200, $response->getStatusCode(), 'Failed in getting widget view');
        $this->assertNotEmpty($crawler->html());
        self::assertStringContainsString('Quotes', $crawler->html());
    }

    /**
     * @dataProvider gridProvider
     */
    public function testGrid(array $requestData)
    {
        $this->manager->deactivateWorkflow('b2b_quote_backoffice_approvals');
        $this->manager->deactivateWorkflow('b2b_quote_backoffice_default');

        $requestData['gridParameters'] = array_replace(
            $requestData['gridParameters'],
            [
                'opportunity-quotes-grid[opportunity_id]' => $this->getReference('opportunity')->getId()
            ]
        );

        parent::testGrid($requestData);

        $result = self::jsonToArray($this->client->getResponse()->getContent());

        if (!empty($requestData['assertRowActions'])) {
            foreach ($result['data'] as $row) {
                $this->assertNotEmpty($row['action_configuration'], 'No available row actions');

                $rowActions = array_keys($row['action_configuration']);
                $notAvailableActions = array_diff($requestData['assertRowActions'], $rowActions);

                $this->assertEmpty(
                    $notAvailableActions,
                    'Not all required row actions are available'
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function gridProvider(): array
    {
        return [
            'Quote grid CRUD + Expire actions available' => [
                [
                    'gridParameters'      => [
                        'gridName' => 'opportunity-quotes-grid',
                        'opportunity-quotes-grid[opportunity_id]' => '', //will be set later
                    ],
                    'gridFilters'         => [],
                    'assert'              => [],
                    'assertRowActions'       => [
                        'oro_sale_expire_quote',
                        'opportunity_quotes_update',
                        'opportunity_quotes_delete'
                    ],
                    'expectedResultCount' => 1
                ],
            ]
        ];
    }
}
