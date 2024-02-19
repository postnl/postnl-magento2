<?php

namespace TIG\PostNL\Config\Comment;

use Magento\Config\Model\Config\CommentInterface;
use TIG\PostNL\Block\Adminhtml\Config\Comment\LabelSize as Block;

class LabelSize implements CommentInterface
{
    /**
     * @var Block
     */
    private $block;

    /**
     * @param Block $block
     */
    public function __construct(
        Block $block
    ) {
        $this->block = $block;
    }

    /**
     * Retrieve element comment by element value
     *
     * @param string $elementValue
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    public function getCommentText($elementValue)
    {
        return $this->block->toHtml();
    }
}
