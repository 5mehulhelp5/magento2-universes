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
use Blackbird\Universes\Model\Config\Universes as UniversesConfig;
use Magento\Cms\Model\Page;

class HomepageInUniverse implements UniverseStrategyInterface
{
    /**
     * @param RequestHandler $requestHandler
     * @param UniversesConfig $universesConfig
     * @param Page $page
     */
    public function __construct(
        protected RequestHandler $requestHandler,
        protected UniversesConfig $universesConfig,
        protected Page $page
    ) {
    }

    /**
     * @param array $universesConfig
     *
     * @return array
     */
    public function execute(array $universesConfig): array
    {
        $pageId = $this->page->getId();

        $result = [];

        foreach ($universesConfig as $universe) {
            $homepageId = $universe[UniversesInterface::HOMEPAGE];

            if (
                $this->universesConfig->isValueEmpty($homepageId) ||
                !$this->isHomepageInUniverse((int) $homepageId, (int) $pageId)
            ) {
                continue;
            }

            $result = $universe;

            break;
        }

        return $result;
    }

    /**
     * @param int $homepageId
     * @param int $pageId
     *
     * @return bool
     */
    public function isHomepageInUniverse(int $homepageId, int $pageId): bool
    {
        return $homepageId === $pageId;
    }
}
