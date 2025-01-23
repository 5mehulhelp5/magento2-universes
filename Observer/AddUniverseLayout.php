<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Blackbird\Universes\Model\Service\Formatter;
use Blackbird\Universes\Api\UniverseResolverInterface;
use Magento\Framework\App\Request\Http as RequestHandler;
use Blackbird\Universes\Api\Data\UniversesInterface;
use Blackbird\Universes\Model\Config\Universes as UniversesConfig;
use Magento\Framework\View\Page\Config as PageConfig;

class AddUniverseLayout implements ObserverInterface
{
    /**
     * @param LoggerInterface $logger
     * @param Formatter $formatter
     * @param UniverseResolverInterface $universeResolver
     * @param RequestHandler $requestHandler
     * @param UniversesConfig $universesConfig
     * @param PageConfig $pageConfig
     */
    public function __construct(
        protected LoggerInterface $logger,
        protected Formatter $formatter,
        protected UniverseResolverInterface $universeResolver,
        protected RequestHandler $requestHandler,
        protected UniversesConfig $universesConfig,
        protected PageConfig $pageConfig
    ) {
    }

    /**
     * @param string $universeLabel
     *
     * @return string
     */
    public function getUniverseLayoutName(string $universeLabel): string
    {
        return $this->formatter->formatUniverseLabel($universeLabel) . '_' . $this->requestHandler->getFullActionName();
    }

    /**
     * @param string $universeLabel
     *
     * @return string
     */
    public function getUniverseDefaultLayoutName(string $universeLabel): string
    {
        return $this->formatter->formatUniverseLabel($universeLabel) . '_default';
    }

    /**
     * @param string $universeLabel
     *
     * @return string
     */
    public function getUniverseBodyClassName(string $universeLabel): string
    {
        return $this->formatter->formatUniverseLabel($universeLabel) . '-universe';
    }

    /**
     * @param Observer $observer
     *
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        try {
            $universeContext = $this->universeResolver->resolveUniverseContext();

            if (empty($universeContext)) {
                return;
            }

            $universeLabel = $universeContext[UniversesInterface::LABEL];

            if ($this->universesConfig->isValueEmpty($universeLabel)) {
                return;
            }

            $layout = $observer->getData('layout');

            $layout->getUpdate()->addHandle($this->getUniverseLayoutName($universeLabel));
            $layout->getUpdate()->addHandle($this->getUniverseDefaultLayoutName($universeLabel));

            $this->pageConfig->addBodyClass($this->getUniverseBodyClassName($universeLabel));
        } catch (NoSuchEntityException | LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
