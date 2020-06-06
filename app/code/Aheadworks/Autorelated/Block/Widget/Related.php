<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block\Widget;

use Aheadworks\Autorelated\Block\Related as BlockRelated;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Model\Source\Position;

class Related extends BlockRelated implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var string
     */
    const WIDGET_NAME_PREFIX = 'aw_arp_widget_';

    /**
     * Retrieve block for widget
     *
     * @return \Aheadworks\Autorelated\Api\Data\BlockInterface[]
     */
    public function getBlocks()
    {
        $ruleId = $this->getData('rule_id');
        $blocks = $this->blocksRepository
                ->getList(Type::CUSTOM_BLOCK_TYPE, Position::CUSTOM, true, null, [$ruleId])
                ->getItems();
        foreach ($blocks as $block) {
            if ($block->getRule()->getId() == $ruleId) {
                return [$block];
            }
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNameInLayout()
    {
        return self::WIDGET_NAME_PREFIX . $this->getData('rule_id');
    }
}
