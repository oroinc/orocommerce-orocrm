<?php

namespace Oro\Bridge\QuoteSales\EventListener;

use Oro\Bridge\QuoteSales\Provider\OpportunityQuotesProvider;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SalesBundle\Entity\Opportunity;
use Oro\Bundle\UIBundle\Event\BeforeViewRenderEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Adds block with relevant opportunities grid on the Opportunity view.
 */
class OpportunityQuotesListener
{
    // below activity block which have 1000
    const GRID_BLOCK_PRIORITY = 1005;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var DoctrineHelper  */
    protected $doctrineHelper;

    public function __construct(
        OpportunityQuotesProvider $opportunityQuotesProvider,
        TranslatorInterface $translator
    ) {
        $this->opportunityQuotesProvider        = $opportunityQuotesProvider;
        $this->translator                       = $translator;
    }

    /**
     * Adds block with relevant opportunities grid on the Opportunity view
     */
    public function addRelatedQuotes(BeforeViewRenderEvent $event)
    {
        $data        = $event->getData();
        $entity      = $event->getEntity();
        $environment = $event->getTwigEnvironment();

        if (!$entity instanceof Opportunity) {
            return;
        }

        if (!$entity->getCustomerAssociation()) {
            return;
        }

        if (!count($this->opportunityQuotesProvider->getQuotesByOpportunity($entity))) {
            return;
        }

        $quotesData = $environment->render(
            '@OroQuoteSalesBridge/Opportunity/opportunityQuotes.html.twig',
            [
                'gridParams' =>
                    [
                        'opportunity_id' => $entity->getId(),
                        'related_entity_class' => Opportunity::class
                    ]
            ]
        );

        $data['dataBlocks'][] = [
            'title'     => $this->translator->trans('oro.sale.quote.entity_plural_label'),
            'priority'  => self::GRID_BLOCK_PRIORITY,
            'subblocks' => [['data' => [$quotesData]]]
        ];

        $event->setData($data);
    }
}
