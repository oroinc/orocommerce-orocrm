<?php

namespace Oro\Bridge\SaleActivityContact\Tests\Functional\Action;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;

use Oro\Bundle\ActionBundle\Tests\Functional\ActionTestCase;
use Oro\Bundle\RFPBundle\Tests\Functional\DataFixtures\LoadRequestData;
use Oro\Bundle\SaleBundle\Tests\Functional\DataFixtures\LoadQuoteData;

class ActionGroupTest extends ActionTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->initClient();
        $this->loadFixtures([LoadRequestData::class, LoadQuoteData::class]);
    }

    public function testOroRfpDuplicate()
    {
        $request = $this->getEntityAndSetContactFields(LoadRequestData::REQUEST2);

        $actionData = $this->executeActionGroup('oro_rfp_duplicate', ['request' => $request]);

        $this->assertActivityContactFieldsEmpty($actionData->offsetGet('requestCopy'));
    }

    public function testOroSaleQuoteDuplicate()
    {
        $quote = $this->getEntityAndSetContactFields(LoadQuoteData::QUOTE_DRAFT);

        $actionData = $this->executeActionGroup('oro_sale_quote_duplicate', ['quote' => $quote]);

        $this->assertActivityContactFieldsEmpty($actionData->offsetGet('quoteCopy'));
    }

    /**
     * @param object $entity
     */
    protected function assertActivityContactFieldsEmpty($entity)
    {
        $this->assertNull($entity->getAcLastContactDateIn());
        $this->assertNull($entity->getAcLastContactDateOut());
        $this->assertNull($entity->getAcLastContactDate());
        $this->assertEquals(0, $entity->getAcContactCountIn());
        $this->assertEquals(0, $entity->getAcContactCountOUt());
        $this->assertEquals(0, $entity->getAcContactCount());
    }

    /**
     * @param string $reference
     * @return object
     */
    private function getEntityAndSetContactFields($reference)
    {
        $entity = $this->getReference($reference);
        $this->assertNotNull($entity);

        $em = $this->getManagerForEntity($entity);
        $this->assertNotNull($em);

        $entity->setAcLastContactDate(new \DateTime());
        $entity->setAcLastContactDateIn(new \DateTime());
        $entity->setAcLastContactDateOut(new \DateTime());
        $entity->setAcContactCountIn(1);
        $entity->setAcContactCountOut(2);
        $entity->setAcContactCount(3);

        $em->flush($entity);

        return $entity;
    }

    /**
     * @param object $entity
     * @return ObjectManager
     */
    private function getManagerForEntity($entity)
    {
        return $this->getContainer()->get('doctrine')->getManagerForClass(ClassUtils::getClass($entity));
    }
}
