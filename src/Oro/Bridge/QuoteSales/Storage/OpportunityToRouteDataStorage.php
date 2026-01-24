<?php

namespace Oro\Bridge\QuoteSales\Storage;

use Oro\Bundle\ProductBundle\Storage\DataStorageInterface;
use Oro\Bundle\SalesBundle\Entity\Opportunity;

/**
 * Stores opportunity route data for navigation after quote operations.
 *
 * Manages temporary storage of route information that specifies where to redirect
 * the user after quote-related operations are completed. Stores the target route
 * (typically the opportunity view page) and associated parameters to enable seamless
 * navigation back to the opportunity after quote creation or modification.
 */
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
