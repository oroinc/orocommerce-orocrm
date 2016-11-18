<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

/**
 * Interface for account assign strategies
 */
interface AssignStrategyInterface
{
    /**
     * Process entity according to current strategy
     * Return array entities to update
     *
     * @param $entity
     *
     * @return array
     */
    public function process($entity);

    /**
     * Get strategy identifier
     *
     * @return string
     */
    public function getName();
}
