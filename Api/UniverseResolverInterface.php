<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

namespace Blackbird\Universes\Api;

interface UniverseResolverInterface
{
    public const ENABLE_CACHE_KEY_PREFIX = 'UNIVERSES_CONFIGS_ENABLE_WEBSITE_';
    public const CONFIGS_CACHE_KEY_PREFIX = 'UNIVERSES_CONFIGS_WEBSITE_';

    /**
     * @param string|null $universeLabel
     *
     * @return array
     */
    public function resolveUniverseContext(?string $universeLabel = null): array;

    /**
     * @param int $websiteId
     * @param string|null $universeLabel
     *
     * @return array
     */
    public function getEligibleUniverses(int $websiteId, ?string $universeLabel = null): array;

    /**
     * @param int $websiteId
     * @param string $actionName
     *
     * @return bool
     */
    public function isUniverseProcessable(int $websiteId, string $actionName): bool;
}
