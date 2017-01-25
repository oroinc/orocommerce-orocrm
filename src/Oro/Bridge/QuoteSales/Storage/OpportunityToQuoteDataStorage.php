<?php

namespace Oro\Bridge\QuoteSales\Storage;

use Oro\Bundle\ProductBundle\Storage\ProductDataStorage;
use Oro\Bundle\SalesBundle\Entity\Opportunity;

class OpportunityToQuoteDataStorage
{
    /**
     * @var ProductDataStorage
     */
    protected $storage;

    /**
     * OpportunityToQuoteDataStorage constructor.
     *
     * @param ProductDataStorage $storage
     */
    public function __construct(ProductDataStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Puts data to storage. This data will be used in some other place (for example QuoteController)
     * @param Opportunity $opportunity
     */
    public function saveToStorage(Opportunity $opportunity)
    {
        $data = [
            ProductDataStorage::ENTITY_DATA_KEY => [
                'customer' => $opportunity->getCustomerAssociation()->getTarget()->getId(),
                'opportunity' => $opportunity->getId(),
            ],
        ];

        $this->storage->set($data);
    }
}
