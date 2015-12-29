<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPrice;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductService;

class ProductPriceRecalculatorTest extends PHPUnit_Framework_TestCase {

	public function testRunImmediatelyRecalculations() {
		$productMock = $this->getMock(Product::class, null, [], '', false);
		$pricingGroupMock = $this->getMock(PricingGroup::class, null, [], '', false);
		$productServiceMock = $this->getMock(ProductService::class, null, [], '', false);

		$emMock = $this->getMock(EntityManager::class, ['clear', 'flush'], [], '', false);
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
		$productPrice = new ProductPrice(new Price(0, 0), false);
		$productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($productPrice);
		$productCalculatedPriceRepositoryMock = $this->getMock(
			ProductCalculatedPriceRepository::class,
			['saveCalculatedPrice'],
			[],
			'',
			false
		);
		$productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);
		$productPriceRecalculationSchedulerMock->scheduleRecalculatePriceForProduct($productMock);
		$pricingGroupFacadeMock = $this->getMock(PricingGroupFacade::class, ['getAll'], [], '', false);
		$pricingGroupFacadeMock->expects($this->once())->method('getAll')->willReturn([$pricingGroupMock]);

		$productPriceRecalculator = new ProductPriceRecalculator(
			$emMock,
			$productPriceCalculationMock,
			$productCalculatedPriceRepositoryMock,
			$productPriceRecalculationSchedulerMock,
			$pricingGroupFacadeMock,
			$productServiceMock
		);

		$productPriceRecalculator->runImmediateRecalculations();
	}

}
