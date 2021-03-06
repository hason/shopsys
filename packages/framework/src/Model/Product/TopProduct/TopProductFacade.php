<?php

namespace Shopsys\FrameworkBundle\Model\Product\TopProduct;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetailFactory;

class TopProductFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\TopProduct\TopProductRepository
     */
    protected $topProductRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetailFactory
     */
    protected $productDetailFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\TopProduct\TopProductFactoryInterface
     */
    protected $topProductFactory;

    public function __construct(
        EntityManagerInterface $em,
        TopProductRepository $topProductRepository,
        ProductDetailFactory $productDetailFactory,
        TopProductFactoryInterface $topProductFactory
    ) {
        $this->em = $em;
        $this->topProductRepository = $topProductRepository;
        $this->productDetailFactory = $productDetailFactory;
        $this->topProductFactory = $topProductFactory;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\TopProduct\TopProduct[]
     */
    public function getAll($domainId)
    {
        return $this->topProductRepository->getAll($domainId);
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetail[]
     */
    public function getAllOfferedProductDetails($domainId, $pricingGroup)
    {
        $products = $this->topProductRepository->getOfferedProductsForTopProductsOnDomain($domainId, $pricingGroup);
        return $this->productDetailFactory->getDetailsForProducts($products);
    }

    /**
     * @param $domainId
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     */
    public function saveTopProductsForDomain($domainId, array $products)
    {
        $oldTopProducts = $this->topProductRepository->getAll($domainId);
        foreach ($oldTopProducts as $oldTopProduct) {
            $this->em->remove($oldTopProduct);
        }
        $this->em->flush($oldTopProducts);

        $topProducts = [];
        $position = 1;
        foreach ($products as $product) {
            $topProduct = $this->topProductFactory->create($product, $domainId, $position++);
            $this->em->persist($topProduct);
            $topProducts[] = $topProduct;
        }
        $this->em->flush($topProducts);
    }
}
