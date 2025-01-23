<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\Block\System\Config\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPagesCollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\Collection as CmsPagesCollection;
use Magento\Cms\Model\Page;
use Exception;
use Psr\Log\LoggerInterface;

class CmsPagesList extends Select
{
    /**
     * @param Context $context
     * @param CmsPagesCollectionFactory $cmsPagesCollectionFactory
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        protected CmsPagesCollectionFactory $cmsPagesCollectionFactory,
        protected LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }

    /**
     * @return CmsPagesCollection|array
     */
    private function getPagesCollection(): CmsPagesCollection|array
    {
        try {
            return $this->cmsPagesCollectionFactory->create();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return [];
        }
    }

    /**
     * @return string
     */
    public function _toHtml(): string
    {
        $pages = $this->getPagesCollection()->getItems();

        if (count($pages) > 0 && !$this->getOptions()) {
            // Visually see titles in the best alphabetical order possible
            \usort($pages, static fn($a, $b) => \strcasecmp($a->getTitle(), $b->getTitle()));

            foreach ($pages as $page) {
                /** @var Page $page */
                $this->addOption($page->getId(), $page->getTitle());
            }
        }

        return parent::_toHtml();
    }
}
