<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Block\Adminhtml\Config\Support;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;
use TIG\PostNL\Config\Provider\PostNLConfiguration;

class SupportTab extends Template implements RendererInterface
{
    const POSTNL_VERSION = '1.12.5';

    const XPATH_SUPPORTED_MAGENTO_VERSION = 'tig_postnl/supported_magento_version';

    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::config/support/supportTab.phtml';

    /**
     * @var PostNLConfiguration
     */
    private $configuration;

    /**
     * Override the parent constructor to require our own dependencies.
     *
     * @param Template\Context    $context
     * @param PostNLConfiguration $configuration
     * @param array               $data
     */
    public function __construct(
        Template\Context $context,
        PostNLConfiguration $configuration,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configuration = $configuration;
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
        return static::POSTNL_VERSION;
    }

    /**
     * @return string
     */
    public function getSupportedMagentoVersions()
    {
        return $this->configuration->getSupportedMagentoVersions();
    }

    /**
     * @return string
     */
    public function getStability()
    {
        $stability = $this->configuration->getStability();

        if ($stability === null || $stability == 'stable') {
            return '';
        }

        return ' - ' . ucfirst($stability);
    }

    public function forBusinessUrl()
    {
        return __('https://www.postnl.nl/zakelijke-oplossingen/webwinkels/bezorgopties-voor-mijn-klanten/');
    }
}
