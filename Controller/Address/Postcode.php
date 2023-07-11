<?php

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
            return $this->returnJson($this->getErrorResponse('error', __('Postcode request validation failed')));
        }

        $this->postcodeService->updateRequestData($params);
        $result = $this->postcodeService->call();
        $result = $this->handler->convertResponse($result);

        if ($result === false) {
            return $this->returnJson($this->getErrorResponse(false, __('Zipcode/housenumber combination not found')));
        }

        if ($result === 'error') {
            return $this->returnJson($this->getErrorResponse('error', __('Postcode response validation failed')));
        }

        return $this->returnJson($result);
    }

    /**
     * @param $status string|bool
     * @param $error string
     *
     * @return array
     */
    private function getErrorResponse($status, $error)
    {
        $responseArray = [
            'status' => $status,
            'error'  => $error
        ];
        return $responseArray;
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
