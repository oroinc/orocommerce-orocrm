<?php

namespace Oro\Bridge\QuoteSales\Provider;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OpportunityQuotesProvider
{
    /** @var RegistryInterface */
    protected $doctrine;

    /** @var AclHelper  */
    protected $aclHelper;

    public function __construct(RegistryInterface $doctrine, AclHelper $aclHelper)
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
