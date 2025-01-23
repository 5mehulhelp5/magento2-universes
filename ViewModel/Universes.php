<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Blackbird\Universes\Api\UniverseResolverInterface;

class Universes implements ArgumentInterface
{
    /**
     * @param UniverseResolverInterface $universeResolver
     */
    public function __construct(protected UniverseResolverInterface $universeResolver)
    {
    }

    /**
     * @return array
     */
    public function getUniverseConfig(): array
    {
        return $this->universeResolver->resolveUniverseContext();
    }

    /**
     * @param string $universeLabel
     *
     * @return bool
     */
    public function isInUniverse(string $universeLabel): bool
    {
        return empty($this->universeResolver->resolveUniverseContext($universeLabel));
    }
}
