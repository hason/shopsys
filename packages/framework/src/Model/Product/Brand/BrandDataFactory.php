<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class BrandDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade
     */
    protected $brandFacade;

    public function __construct(
        FriendlyUrlFacade $friendlyUrlFacade,
        BrandFacade $brandFacade
    ) {
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->brandFacade = $brandFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData
     */
    public function createDefault()
    {
        return new BrandData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData
     */
    public function createFromBrand(Brand $brand)
    {
        $brandData = new BrandData();
        $brandData->name = $brand->getName();

        $translations = $brand->getTranslations();
        /* @var $translations \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation[]  */

        $brandData->descriptions = [];
        foreach ($translations as $translation) {
            $brandData->descriptions[$translation->getLocale()] = $translation->getDescription();
        }

        foreach ($brand->getDomains() as $brandDomain) {
            $domainId = $brandDomain->getDomainId();

            $brandData->seoH1s[$domainId] = $brand->getSeoH1($domainId);
            $brandData->seoTitles[$domainId] = $brand->getSeoTitle($domainId);
            $brandData->seoMetaDescriptions[$domainId] = $brand->getSeoMetaDescription($domainId);

            $brandData->urls->mainFriendlyUrlsByDomainId[$domainId] =
                $this->friendlyUrlFacade->findMainFriendlyUrl(
                    $domainId,
                    'front_brand_detail',
                    $brand->getId()
                );
        }

        return $brandData;
    }
}
