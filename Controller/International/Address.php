<?php

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
