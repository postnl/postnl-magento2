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
namespace TIG\PostNL\Service\Framework;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\App\State;

class ValidationState implements ValidationStateInterface
{
    private $productMetaData;

    private $appMode;

    public function __construct(
        ProductMetadataInterface $productMetadata,
        State $state
    ) {
        $this->productMetaData = $productMetadata;
        $this->appMode = $state->getMode();
    }

    /**
     * Retrieve current validation state
     *
     * Magento 2.1.* uses xsd schemes that are not containing the listingToolbar component.
     * When in developer mode these schemes are triggerd to validate the ui_definition.xml which will break the backend.
     *
     * @return boolean
     */
    public function isValidationRequired()
    {
        if (!version_compare($this->productMetaData->getVersion(), '2.2.0', '<')) {
            return $this->appMode == State::MODE_DEVELOPER;
        }

        return false;
    }
}
