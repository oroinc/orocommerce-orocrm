<?php

namespace Oro\Bridge\QuoteSales\Provider;

use Doctrine\Common\Persistence\ManagerRegistry;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class OpportunityQuotesProvider
{
    /** @var ManagerRegistry */
    protected $doctrine;

    /** @var AclHelper  */
    protected $aclHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param AclHelper $aclHelper
     */
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
        $repo = $this->doctrine->getRepository('OroSaleBundle:Quote');
        $qb = $repo->createQueryBuilder('q');
        $qb->where('q.opportunity = :opportunity');
        $qb->setParameter('opportunity', $opportunity);

        return $this->aclHelper->apply($qb)->getArrayResult();
    }
}
