<?php

namespace Oro\Bridge\QuoteSales\Tests\Unit\EventListener;

use Oro\Bridge\QuoteSales\EventListener\OpportunityQuotesListener;
use Oro\Bridge\QuoteSales\Provider\OpportunityQuotesProvider;
use Oro\Bundle\SalesBundle\Entity\Opportunity;
use Oro\Bundle\UIBundle\Event\BeforeViewRenderEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class OpportunityQuotesListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var OpportunityQuotesListener */
    private $opportunityQuotesListener;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $opportunityQuotesProvider;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $translator;

    protected function setUp(): void
    {
        $this->opportunityQuotesProvider = $this->createMock(OpportunityQuotesProvider::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->opportunityQuotesListener = new OpportunityQuotesListener(
            $this->opportunityQuotesProvider,
            $this->translator
        );
    }

    public function testNoQuotesInOpportunity()
    {
        $this->opportunityQuotesProvider->expects($this->once())
            ->method('getQuotesByOpportunity')
            ->willReturn([]);

        $this->translator->expects($this->never())
            ->method('trans');

        $entity = $this->createMock(Opportunity::class);
        $entity->expects($this->once())
            ->method('getCustomerAssociation')
            ->willReturn(true);

        $event = $this->createMock(BeforeViewRenderEvent::class);
        $event->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);

        $this->opportunityQuotesListener->addRelatedQuotes($event);
    }
}
