<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;

class PricingGroupDataFixture extends AbstractReferenceFixture
{
    /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade */
    private $pricingGroupFacade;

    const PRICING_GROUP_ORDINARY_DOMAIN_2 = 'pricing_group_ordinary_domain_2';
    const PRICING_GROUP_VIP_DOMAIN_2 = 'pricing_group_vip_domain_2';

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
     */
    public function __construct(PricingGroupFacade $pricingGroupFacade)
    {
        $this->pricingGroupFacade = $pricingGroupFacade;
    }

    public function load(ObjectManager $manager)
    {
        $pricingGroupData = new PricingGroupData();

        $pricingGroupData->name = 'Obyčejný zákazník';
        $domainId = 2;
        $this->createPricingGroup($pricingGroupData, $domainId, self::PRICING_GROUP_ORDINARY_DOMAIN_2);

        $pricingGroupData->name = 'VIP zákazník';
        $domainId1 = 2;
        $this->createPricingGroup($pricingGroupData, $domainId1, self::PRICING_GROUP_VIP_DOMAIN_2);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @param int $domainId
     * @param string $referenceName
     */
    private function createPricingGroup(
        PricingGroupData $pricingGroupData,
        $domainId,
        $referenceName
    ) {
        $pricingGroup = $this->pricingGroupFacade->create($pricingGroupData, $domainId);
        $this->addReference($referenceName, $pricingGroup);
    }
}
