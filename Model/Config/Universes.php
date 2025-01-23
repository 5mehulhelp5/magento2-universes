<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\Model\Config;

use JsonException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

use const JSON_THROW_ON_ERROR;

class Universes
{
    private const ENABLED_CONFIG_PATH = 'blackbird_universes/general/enable';
    private const UNIVERSES_CONFIG_PATH = 'blackbird_universes/general/universes';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(protected ScopeConfigInterface $scopeConfig)
    {
    }

    /**
     * @param int|null $websiteId
     *
     * @return bool
     */
    public function areUniversesEnabled(?int $websiteId = null): bool
    {
        return (bool) $this->scopeConfig->getValue(self::ENABLED_CONFIG_PATH, ScopeInterface::SCOPE_WEBSITE, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return array
     */
    public function getUniversesConfig(?int $websiteId = null): array
    {
        try {
            return \json_decode(
                $this->scopeConfig->getValue(self::UNIVERSES_CONFIG_PATH, ScopeInterface::SCOPE_WEBSITE, $websiteId) ?? '{}',
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            return [];
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty(mixed $value): bool
    {
        return \trim($value ?? '') === '';
    }
}
