<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\Model\Service\Strategy;

use Blackbird\Universes\Api\UniverseStrategyInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Blackbird\Universes\Api\Data\UniversesInterface;
use Magento\Framework\App\Request\Http as RequestHandler;
use Magento\Framework\Exception\NoSuchEntityException;
use Blackbird\Universes\Model\Config\Universes as UniversesConfig;

class CategoryInUniverse implements UniverseStrategyInterface
{
    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param RequestHandler $requestHandler
     * @param UniversesConfig $universesConfig
     */
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected RequestHandler $requestHandler,
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
        $categoryId = $this->requestHandler->getParam('id');

        $result = [];

        foreach ($universesConfig as $universe) {
            $rootCategoryId = $universe[UniversesInterface::CATEGORY_ROOT_ID];

            if (
                $this->universesConfig->isValueEmpty($rootCategoryId) ||
                !$this->isCategoryInUniverse($rootCategoryId, $categoryId)
            ) {
                continue;
            }

            $result = $universe;

            break;
        }

        return $result;
    }

    /**
     * @param string $rootCategoryId
     * @param string $categoryId
     *
     * @return bool
     */
    public function isCategoryInUniverse(string $rootCategoryId, string $categoryId): bool
    {
        try {
            $currentCategory = $this->categoryRepository->get($categoryId);

            $parentCategories = \explode('/', $currentCategory->getPath() ?? '');

            return \in_array($rootCategoryId, $parentCategories, true);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
