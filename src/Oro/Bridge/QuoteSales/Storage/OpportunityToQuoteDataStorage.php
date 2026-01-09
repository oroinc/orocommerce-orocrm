<?php

namespace Oro\Bridge\QuoteSales\Storage;

use Oro\Bundle\ProductBundle\Storage\DataStorageInterface;
use Oro\Bundle\ProductBundle\Storage\ProductDataStorage;
use Oro\Bundle\SalesBundle\Entity\Opportunity;

/**
 * Stores opportunity data for use in quote creation workflows.
 *
 * Manages temporary storage of opportunity information (customer and opportunity IDs)
 * that is needed during the quote creation process. This data is persisted in a session
 * or temporary storage and retrieved by the quote controller to pre-populate quote forms
 * with relevant opportunity details.
 */
class OpportunityToQuoteDataStorage
{
    /**
     * @var DataStorageInterface
     */
    protected $attributeStorage;

    /**
     * OpportunityToQuoteDataStorage constructor.
     */
    public function __construct(DataStorageInterface $attributeStorage)
    {
        $this->attributeStorage = $attributeStorage;
    }

    /**
     * Puts data to storage. This data will be used in some other place (for example QuoteController)
     */
    public function saveToStorage(Opportunity $opportunity)
    {
        $data = [
            ProductDataStorage::ENTITY_DATA_KEY => [
                'customer' => $opportunity->getCustomerAssociation()->getTarget()->getId(),
                'opportunity' => $opportunity->getId(),
            ],
        ];

        $this->attributeStorage->set($data);
    }
}
