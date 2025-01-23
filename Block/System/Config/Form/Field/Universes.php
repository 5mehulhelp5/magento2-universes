<?php

/**
 * Universes
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    emilie (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\Block\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Blackbird\Universes\Block\System\Config\Form\Field\CmsPagesList;
use Blackbird\Universes\Api\Data\UniversesInterface;

class Universes extends AbstractFieldArray
{
    /**
     * @param Context $context
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     * @param BlockInterface|null $cmsPagesBlock
     */
    public function __construct(
        Context $context,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null,
        protected ?BlockInterface $cmsPagesBlock = null
    ) {
        parent::__construct($context, $data, $secureRenderer);
    }

    /**
     * @return void
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            UniversesInterface::LABEL,
            [
                'label' => __('Universe Label')
            ]
        );

        $this->addColumn(
            UniversesInterface::HOMEPAGE,
            [
                'label' => __('Homepage'),
                'renderer' => $this->renderCmsPages()
            ]
        );

        $this->addColumn(
            UniversesInterface::CATEGORY_ROOT_ID,
            [
                'label' => __('Category Root Id')
            ]
        );

        $this->addColumn(
            UniversesInterface::PRODUCT_ATTRIBUTE,
            [
                'label' => __('Product Attribute')
            ]
        );

        $this->addColumn(
            UniversesInterface::PRODUCT_VALUE,
            [
                'label' => __('Product Value')
            ]
        );

        $this->_addAfter = false;

        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return BlockInterface|null
     */
    private function renderCmsPages(): ?BlockInterface
    {
        if (!$this->cmsPagesBlock) {
            $this->cmsPagesBlock = $this->getLayout()->createBlock(
                CmsPagesList::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true
                    ]
                ]
            );
        }

        return $this->cmsPagesBlock;
    }

    /**
     * Set the homepages' <select> visually with the right <option>
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $homepageAttribute = $row->getData(UniversesInterface::HOMEPAGE);

        $extraOptionKey = 'option_' . $this->cmsPagesBlock->calcOptionHash($homepageAttribute);

        $row->setData('option_extra_attrs', [$extraOptionKey => 'selected="selected"']);

        $this->setDefaultColumnValues($row);
    }

    /**
     * If there already are values stored in the database as a serialized array for this field, not setting default ones
     * breaks the rendering in case we add new columns
     *
     * @param DataObject $row
     * @return void
     */
    public function setDefaultColumnValues(DataObject $row): void
    {
        $columns = $this->getColumns();
        $rowData = $row->getData();

        foreach ($columns as $key => $column) {
            if (isset($rowData[$key])) {
                continue;
            }

            $rowData[$key] = '';
            $rowData['column_values'][$rowData['_id'] . $key] = '';
        }

        $row->addData($rowData);
    }
}
