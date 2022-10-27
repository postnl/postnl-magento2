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
namespace TIG\PostNL\Controller\Adminhtml\Config\Validate;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use TIG\PostNL\Service\Handler\PostcodecheckHandler;
use TIG\PostNL\Webservices\Endpoints\Address\InternationalAddressCheck;

class InternationalAddressValidation extends Action
{
    /** @var JsonFactory  */
    private $jsonFactory;

    /** @var InternationalAddressCheck  */
    private $internationalAddressCheck;

    /**
     * @param Context                   $context
     * @param JsonFactory               $jsonFactory
     * @param PostcodecheckHandler      $postcodecheckHandler
     * @param InternationalAddressCheck $internationalAddressCheck
     */
    public function __construct(
        Context                   $context,
        JsonFactory               $jsonFactory,
        PostcodecheckHandler      $postcodecheckHandler,
        InternationalAddressCheck $internationalAddressCheck
    ) {
        parent::__construct($context);

        $this->jsonFactory               = $jsonFactory;
        $this->handler                   = $postcodecheckHandler;
        $this->internationalAddressCheck = $internationalAddressCheck;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $params =
            [
                'CountryIso'          => 'NL',
                'cityName'            => 'Amsterdam',
                'PostalCode'          => '1014 BA',
                'streetName'          => 'Kabelweg',
                'houseNumber'         => '37',
                'houseNumberAddition' => null,
                'addressLine'         => null,
                'buildingName'        => null,
                'flat'                => null,
                'stairs'              => null,
                'floor'               => null,
                'door'                => null,
                'bus'                 => null
            ];

        $this->internationalAddressCheck->updateRequestData($params);
        $result = $this->internationalAddressCheck->call();

        if ($result === false) {
            $result = [
                'error' => true,
                //@codingStandardsIgnoreLine
                'message' => __('Your API Credentials could not be validated.')
            ];
        }

        $data = @json_decode($result);

        if ($data !== false && isset($data->fault->faultstring)){
            $result = [
                'error' => true,
                //@codingStandardsIgnoreLine
                'message' => __($data->fault->faultstring)
            ];
        }

        if ($result === 'error') {
            $result = [
                'error' => true,
                //@codingStandardsIgnoreLine
                'message' => __('Something went wrong while validating your credentials.')
            ];
        }

        return $this->returnJson($result);
    }

    /**
     * @param $data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function returnJson($data)
    {
        $response = $this->jsonFactory->create();
        return $response->setData($data);
    }
}
