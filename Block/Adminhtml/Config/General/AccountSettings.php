<?php

namespace TIG\PostNL\Block\Adminhtml\Config\General;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;
use TIG\PostNL\Config\Provider\AccountConfiguration;

class AccountSettings extends Template implements RendererInterface
{
    const EXT_URL_DELIVERYOPTIONS = 'https://www.postnl.nl/zakelijk/e-commerce/flexibele-bezorgopties';
    const EXT_URL_TESTINFORMATION = 'https://postnl.github.io/magento2/?lang=nl#2';

    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::config/general/accountSettings.phtml';

    /**
     * @var AccountConfiguration
     */
    private $accountConfig;

    /**
     * AccountSettings constructor.
     *
     * @param Template\Context     $context
     * @param AccountConfiguration $accountConfiguration
     * @param array                $data
     */
    public function __construct(
        Template\Context $context,
        AccountConfiguration $accountConfiguration,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->accountConfig = $accountConfiguration;
    }

    /**
     * Render form element as HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * @return mixed|string
     */
    public function getModusClass()
    {
        $modus   = $this->accountConfig->getModus();
        $classes = [
            '1' => 'modus_live',
            '2' => 'modus_test',
            '0' => 'modus_off'
        ];

        $className = 'modus_off';
        if (array_key_exists($modus, $classes)) {
            $className = $classes[$modus];
        }

        return $className;
    }

    /**
     * @return string
     */
    public function getInfoUrlForDeliveryoptions()
    {
        return self::EXT_URL_DELIVERYOPTIONS;
    }

    /**
     * @return string
     */
    public function getInfoUrlForTestAccount()
    {
        return self::EXT_URL_TESTINFORMATION;
    }
}
