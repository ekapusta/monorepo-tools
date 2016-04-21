<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductRepositoryTest extends DatabaseTestCase{

	public function getAllListableQueryBuilderProvider() {
		return [
			[1, true, 'Visible and not selling denied product is not listed'],
			[6, false, 'Visible and selling denied product is listed'],
			[53, false, 'Product variant is listed'],
			[148, true, 'Product main variant is not listed'],
		];
	}

	/**
	 * @dataProvider getAllListableQueryBuilderProvider
	 */
	public function testGetAllListableQueryBuilder($productReferenceId, $isExpectedInResult, $failMessage) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllListableQueryBuilder($domain->getId(), $pricingGroup);
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult, $failMessage);
	}

	public function getAllSellableQueryBuilderProvider() {
		return [
			[1, true, 'Visible and not selling denied product is not sellable'],
			[6, false, 'Visible and selling denied product is sellable'],
			[53, true, 'Product variant is not listed'],
			[148, false, 'Product main variant is listed'],
		];
	}

	/**
	 * @dataProvider getAllSellableQueryBuilderProvider
	 */
	public function testGetAllSellableQueryBuilder($productReferenceId, $isExpectedInResult, $failMessage) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllSellableQueryBuilder($domain->getId(), $pricingGroup);
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult, $failMessage);
	}

	public function getAllOfferedQueryBuilderProvider() {
		return [
			[1, true, 'Visible and not selling denied product is not offered'],
			[6, false, 'Visible and selling denied product is offered'],
			[53, true, 'Product variant is not offered'],
			[69, true, 'Product main variant is not offered'],
		];
	}

	/**
	 * @dataProvider getAllOfferedQueryBuilderProvider
	 */
	public function testGetAllOfferedQueryBuilder($productReferenceId, $isExpectedInResult, $failMessage) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllOfferedQueryBuilder($domain->getId(), $pricingGroup);
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult, $failMessage);
	}

}
