<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Support;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;
use TIG\PostNL\Config\Provider\PostNLConfiguration;

class SupportTab extends Template implements RendererInterface
{
    const POSTNL_VERSION = '1.21.0';

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
