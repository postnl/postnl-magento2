<?php

namespace TIG\PostNL\Controller\Adminhtml\Config\Validate;

use Magento\Backend\App\Action;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Store\Model\StoreManagerInterface;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Webservices\Api\Customer;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Endpoints\Barcode;
use TIG\PostNL\Webservices\Soap;

class ApiCredentials extends Action
{
    const POSTNL_TYPE  = '3S';
    const POSTNL_SERIE = '000000000-999999999';

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var Soap
     */
    private $soap;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @var Barcode
     */
    private $barcode;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Log
     */
    private $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param EncoderInterface                    $encoder
     * @param Soap                                $soap
     * @param Message                             $message
     * @param Customer                            $customer
     * @param AccountConfiguration                $accountConfiguration
     * @param Barcode                             $barcode
     * @param StoreManagerInterface               $storeManager
     * @param Log                                 $logger
     */
    public function __construct(
        Action\Context $context,
        EncoderInterface $encoder,
        Soap $soap,
        Message $message,
        Customer $customer,
        AccountConfiguration $accountConfiguration,
        Barcode $barcode,
        StoreManagerInterface $storeManager,
        Log $logger
    ) {
        parent::__construct($context);
        $this->encoder = $encoder;
        $this->soap = $soap;
        $this->message = $message;
        $this->customer = $customer;
        $this->accountConfiguration = $accountConfiguration;
        $this->barcode = $barcode;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    public function execute()
    {
        $result = [
            'error' => true,
            //@codingStandardsIgnoreLine
            'message' => __('Your API Credentials could not be validated, check your data in the My PostNL environment. Or check the PostNL logs.')
        ];

        $storeId = $this->getRequest()->getParam('storeId');
        $websiteId = $this->getRequest()->getParam('websiteId');

        $scopeId = $storeId === '' ? $websiteId : $storeId;

        if ($scopeId === '') {
            $scopeId = $this->storeManager->getStore()->getId();
        }

        $this->soap->updateApiKey($scopeId, ($websiteId !== ''));
        $customerData = $this->getCustomerData($this->getRequest());
        $validatedApiCredentials = $this->validateApiCredentials($customerData);

        if ($validatedApiCredentials) {
            $result['error'] = false;
            $result['message'] = __('Successfully connected to account.');
        }

        $json = $this->encoder->encode($result);
        $response = $this->getResponse();
        return $response->representJson($json);
    }

    /**
     * @param $customerData
     *
     * @return bool|mixed
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    public function validateApiCredentials($customerData)
    {
        $parameters = [
            'Message'  => $this->message->get(''),
            'Customer' => $customerData,
            'Barcode'  => [
                'Type'  => self::POSTNL_TYPE,
                'Range' => $customerData['CustomerCode'],
                'Serie' => self::POSTNL_SERIE,
            ],
        ];

        try {
            return $this->soap->call($this->barcode, 'GenerateBarcode', $parameters);
        } catch (Exception $exception) {
            $this->logger->debug($exception->getMessage());
            return false;
        }
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    public function getCustomerData($request)
    {
        $modus          = $request->getParam('modus');
        $customerCode   = $request->getParam('customer_code');
        $customerNumber = $request->getParam('customer_number');

        if ($modus === '2') {
            $customerCode   = $request->getParam('test_customer_code');
            $customerNumber = $request->getParam('test_customer_number');
        }

        $customerData['CustomerCode']   = $customerCode;
        $customerData['CustomerNumber'] = $customerNumber;

        return $customerData;
    }
}
