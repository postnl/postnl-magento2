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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Block\Frontend\Customer\Address;

use Magento\Backend\Block\Template;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\BlockInterface;

class Edit extends Template implements BlockInterface
{
    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::customer/address/Postcode.phtml';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Template\Context $context
     * @param UrlInterface     $urlBuilder
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function getPostcodeUrl()
    {
        return $this->urlBuilder->getUrl('postnl/address/postcode', ['_secure' => true]);
    }
}
