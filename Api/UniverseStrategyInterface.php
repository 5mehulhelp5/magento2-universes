<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

namespace Blackbird\Universes\Api;

interface UniverseStrategyInterface
{
    /**
     * @param array $universesConfig
     *
     * @return array
     */
    public function execute(array $universesConfig): array;
}
