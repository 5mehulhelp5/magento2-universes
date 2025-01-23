<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\Model\Service\Strategy;

use Blackbird\Universes\Api\Data\UniversesInterface;
use Blackbird\Universes\Api\UniverseStrategyInterface;
use Magento\Framework\App\Request\Http as RequestHandler;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Blackbird\Universes\Model\Config\Universes as UniversesConfig;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductInUniverse implements UniverseStrategyInterface
{
    /**
     * @param RequestHandler $requestHandler
     * @param ProductRepositoryInterface $productRepository
     * @param UniversesConfig $universesConfig
     */
    public function __construct(
        protected RequestHandler $requestHandler,
        protected ProductRepositoryInterface $productRepository,
        protected UniversesConfig $universesConfig
    ) {
    }

    /**
     * @param array $universesConfig
     *
     * @return array
     */
    public function execute(array $universesConfig): array
    {
        $productId = (int) $this->requestHandler->getParam('id');

        $result = [];

        foreach ($universesConfig as $universe) {
            $relatedProductAttribute = $universe[UniversesInterface::PRODUCT_ATTRIBUTE];
            $relatedProductAttributeValue = $universe[UniversesInterface::PRODUCT_VALUE];

            if (
                $this->universesConfig->isValueEmpty($relatedProductAttribute) ||
                $this->universesConfig->isValueEmpty($relatedProductAttributeValue) ||
                !$this->isProductInUniverse($productId, $relatedProductAttribute, $relatedProductAttributeValue)
            ) {
                continue;
            }

            $result = $universe;

            break;
        }

        return $result;
    }

    /**
     * @param int $productId
     * @param string $relatedProductAttribute
     * @param string $relatedProductAttributeValue
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isProductInUniverse(
        int $productId,
        string $relatedProductAttribute,
        string $relatedProductAttributeValue
    ): bool {
        try {
            $product = $this->productRepository->getById($productId);

            return ($product->getData($relatedProductAttribute) ?? '') === $relatedProductAttributeValue ||
                (strtolower((string) $product->getAttributeText($relatedProductAttribute))) === strtolower($relatedProductAttributeValue);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
