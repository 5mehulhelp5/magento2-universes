<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\Model\Service;

use Blackbird\Universes\Model\Config\Universes as UniversesConfig;
use Blackbird\Universes\Model\Service\Strategy\CategoryInUniverse;
use Blackbird\Universes\Model\Service\Strategy\HomepageInUniverse;
use Blackbird\Universes\Model\Service\Strategy\ProductInUniverse;
use Exception;
use Magento\Framework\App\Request\Http as RequestHandler;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Blackbird\Universes\Api\UniverseResolverInterface;
use Blackbird\Universes\Exception\UniverseContextNotProcessable;
use Blackbird\Universes\Api\Data\UniversesInterface;
use Magento\Framework\App\Cache\Type\Config as CacheConfig;
use Magento\Framework\Serialize\Serializer\Json;

class UniverseResolver implements UniverseResolverInterface
{
    public const AUTHORIZED_ACTIONS = [
        'cms_index_index',
        'cms_page_view',
        'catalog_category_view',
        'catalog_product_view'
    ];

    /**
     * @param RequestHandler $requestHandler
     * @param StoreManagerInterface $storeManager
     * @param UniversesConfig $universesConfig
     * @param ProductInUniverse $productInUniverse
     * @param CategoryInUniverse $categoryInUniverse
     * @param HomepageInUniverse $homepageInUniverse
     * @param CacheConfig $cacheConfig
     * @param Json $json
     */
    public function __construct(
        protected RequestHandler $requestHandler,
        protected StoreManagerInterface $storeManager,
        protected UniversesConfig $universesConfig,
        protected ProductInUniverse $productInUniverse,
        protected CategoryInUniverse $categoryInUniverse,
        protected HomepageInUniverse $homepageInUniverse,
        protected CacheConfig $cacheConfig,
        protected Json $json
    ) {
    }

    /**
     * @param int $websiteId
     *
     * @return string
     */
    private function getUniversesConfigCacheKey(int $websiteId): string
    {
        return UniverseResolverInterface::CONFIGS_CACHE_KEY_PREFIX . $websiteId;
    }

    /**
     * @param int $websiteId
     *
     * @return string
     */
    private function getUniversesEnabledCacheKey(int $websiteId): string
    {
        return UniverseResolverInterface::ENABLE_CACHE_KEY_PREFIX . $websiteId;
    }

    /**
     * @return int|string
     *
     * @throws LocalizedException
     */
    private function getCurrentWebsiteId(): int|string
    {
        return $this->storeManager->getWebsite()->getId();
    }

    /**
     * @param string|null $universeLabel
     *
     * @return array
     */
    public function resolveUniverseContext(?string $universeLabel = null): array
    {
        try {
            $actionName = $this->requestHandler->getFullActionName();

            $currentWebsiteId = (int)$this->getCurrentWebsiteId();

            if (!$this->isUniverseProcessable($currentWebsiteId, $actionName)) {
                throw new UniverseContextNotProcessable(
                    __('The universe context is not processable for this action or website.')
                );
            }

            $eligibleUniverses = $this->getEligibleUniverses($currentWebsiteId, $universeLabel);

            if (empty($eligibleUniverses)) {
                throw new UniverseContextNotProcessable(
                    __("The universe context isn't set for this website.")
                );
            }

            if (in_array($actionName, ['cms_index_index', 'cms_page_view'])) {
                return $this->homepageInUniverse->execute($eligibleUniverses);
            }

            if ($actionName === 'catalog_category_view') {
                return $this->categoryInUniverse->execute($eligibleUniverses);
            }

            return $this->productInUniverse->execute($eligibleUniverses);
        } catch (Exception|LocalizedException|UniverseContextNotProcessable $e) {
            return [];
        }
    }

    /**
     * @param int $websiteId
     * @param string|null $universeLabel
     *
     * @return array
     */
    public function getEligibleUniverses(int $websiteId, ?string $universeLabel = null): array
    {
        $cacheKey = $this->getUniversesConfigCacheKey($websiteId);

        $universesConfigCache = $this->cacheConfig->load($cacheKey);

        if ($universesConfigCache) {
            $configuredUniverses = $this->json->unserialize($universesConfigCache);
        } else {
            $configuredUniverses = $this->universesConfig->getUniversesConfig($websiteId);

            $this->cacheConfig->save($this->json->serialize($configuredUniverses), $cacheKey);
        }

        if (empty($configuredUniverses) || $universeLabel === null) {
            return $configuredUniverses;
        }

        return \array_filter(
            $configuredUniverses,
            static fn($universe) => $universe[UniversesInterface::LABEL] === $universeLabel
        );
    }

    /**
     * @param int $websiteId
     * @param string $actionName
     *
     * @return bool
     */
    public function isUniverseProcessable(int $websiteId, string $actionName): bool
    {
        return $this->universesConfig->areUniversesEnabled($websiteId) && \in_array($actionName,
                self::AUTHORIZED_ACTIONS, true);
    }
}
