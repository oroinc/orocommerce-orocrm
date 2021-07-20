<?php

namespace Oro\Bridge\QuoteSales\Storage;

use Oro\Bundle\ProductBundle\Storage\DataStorageInterface;
use Oro\Bundle\SalesBundle\Entity\Opportunity;

class OpportunityToRouteDataStorage
{
    /**
     * @var DataStorageInterface
     */
    protected $returnRouteDataStorage;

    /**
     * OpportunityToQuoteDataStorage constructor.
     */
    public function __construct(DataStorageInterface $returnRouteDataStorage)
    {
        $this->returnRouteDataStorage = $returnRouteDataStorage;
    }

    public function saveToStorage(Opportunity $opportunity)
    {
        $this->returnRouteDataStorage->set([
            'route' => 'oro_sales_opportunity_view',
            'parameters' => [
                'id' => $opportunity->getId()
            ]
        ]);
    }
}
