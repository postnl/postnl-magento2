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
namespace TIG\PostNL\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use TIG\PostNL\Webservices\Endpoints\Address\Postalcode;
use TIG\PostNL\Service\Handler\PostcodecheckHandler;

class Postcode extends Action
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var Postalcode
     */
    private $postcodeService;

    /**
     * @var PostcodecheckHandler
     */
    private $handler;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Postalcode $postalcodeService,
        PostcodecheckHandler $postcodecheckHandler
    ) {
        parent::__construct($context);

        $this->jsonFactory     = $jsonFactory;
        $this->postcodeService = $postalcodeService;
        $this->handler         = $postcodecheckHandler;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $params = $this->handler->convertRequest($params);
        if (!$params) {
            return $this->returnJson([
                'status' => false,
                'error'  => __('Request validation failed')
            ]);
        }

        $this->postcodeService->setRequestData($params);
        $result = $this->postcodeService->call();
        if (!$result) {
            return $this->returnJson([
                'status' => false,
                'error'  => __('Response validation failed')
            ]);
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
