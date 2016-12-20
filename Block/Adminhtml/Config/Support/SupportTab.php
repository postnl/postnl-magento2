<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Block\Adminhtml\Config\Support;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class SupportTab extends \Magento\Framework\View\Element\Template implements RendererInterface
{
    // @codingStandardsIgnoreStart
    protected $_template = 'TIG_PostNL::config/support/supportTab.phtml';
    // @codingStandardsIgnoreEnd

    /**
     * @var \Magento\Framework\Setup\ModuleContextInterface
     */
    protected $moduleContext;

    /**
     * Override the parent constructor to require our own dependencies.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Module\ModuleResource         $moduleContext
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Module\ModuleResource $moduleContext,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->moduleContext = $moduleContext;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * Retrieve the version number from the database.
     *
     * @return bool|false|string
     */
    public function getVersionNumber()
    {
        $version = $this->moduleContext->getDbVersion('TIG_PostNL');

        return $version;
    }
}
