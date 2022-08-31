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
namespace TIG\PostNL\Controller\International;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use TIG\PostNL\Webservices\Endpoints\Address\InternationalAddressCheck;
use TIG\PostNL\Service\Handler\InternationalAddressHandler;

class Address extends Action
{
    /** @var JsonFactory */
    private $jsonFactory;

    /** @var InternationalAddressCheck */
    private $addressCheckService;

    /** @var InternationalAddressHandler */
    private $handler;

    /**
     * @param Context                     $context
     * @param JsonFactory                 $jsonFactory
     * @param InternationalAddressCheck   $addressCheckService
     * @param InternationalAddressHandler $internationalAddressHandler
     */
    public function __construct(
        Context                     $context,
        JsonFactory                 $jsonFactory,
        InternationalAddressCheck   $addressCheckService,
        InternationalAddressHandler $internationalAddressHandler
    ) {
        parent::__construct($context);

        $this->jsonFactory         = $jsonFactory;
        $this->addressCheckService = $addressCheckService;
        $this->handler             = $internationalAddressHandler;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $params = $this->handler->convertRequest($params);

        if (!$params) {
            return $this->returnJson(400, [
                'message' => __('Address request validation failed')
            ]);
        }

        $this->addressCheckService->updateRequestData($params);

        $result          = $this->addressCheckService->call();
        list($statusCode, $formattedResult) = $this->handler->convertResponse($result, $params);

        return $this->returnJson($statusCode, $formattedResult);
    }

    /**
     * @param $statusCode
     * @param $data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function returnJson($statusCode, $data)
    {
        $response = $this->jsonFactory->create();
        $response->setHttpResponseCode($statusCode);
        return $response->setData($data);
    }
}
