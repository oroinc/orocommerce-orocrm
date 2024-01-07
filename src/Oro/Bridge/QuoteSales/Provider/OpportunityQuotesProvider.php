<?php

namespace Oro\Bridge\QuoteSales\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\SaleBundle\Entity\Quote;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

/**
 * Provides quotes by opportunity
 */
class OpportunityQuotesProvider
{
    /** @var ManagerRegistry */
    protected $doctrine;

    /** @var AclHelper  */
    protected $aclHelper;

    public function __construct(ManagerRegistry $doctrine, AclHelper $aclHelper)
    {
        $this->doctrine = $doctrine;
        $this->aclHelper = $aclHelper;
    }

    /**
     * @param $opportunity
     * @return array
     */
    public function getQuotesByOpportunity($opportunity)
    {
        $repo = $this->doctrine->getRepository(Quote::class);
        $qb = $repo->createQueryBuilder('q');
        $qb->where('q.opportunity = :opportunity');
        $qb->setParameter('opportunity', $opportunity);

        return $this->aclHelper->apply($qb)->getArrayResult();
    }
}
