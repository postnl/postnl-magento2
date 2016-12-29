<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Webservices;

use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Webapi\Exception;
use TIG\PostNL\Config\Provider\AccountConfiguration;

/**
 * Class Soap
 *
 * @package TIG\PostNL\Webservices
 */
class SoapOld
{
    /**
     * Header security namespace. Used for constructing the SOAP headers array.
     */
    const HEADER_NAMESPACE = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

    /** TEMP TEST DATA
     *  @todo : Remove when PostNL API is fixed.
     */
    const TEST_SERVICE_WSDL_URL = 'https://testservice.postnl.com/CIF_SB/';
    const TEST_USERNAME         = 'Dem0#Magnt01';
    const TEST_PASSWORD         = '91609cb721bda4c7c9dd855798535c18b6e56629';

    /** @var AccountConfiguration */
    protected $accountConfig;

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManagerInterface
     * @param AccountConfiguration   $accountConfiguration
     *
     * @throws Exception
     */
    public function __construct(
        ObjectManagerInterface $objectManagerInterface,
        AccountConfiguration $accountConfiguration
    ) {
        $this->checkSoapExtensionIsLoaded();

        $this->objectManager = $objectManagerInterface;
        $this->accountConfig = $accountConfiguration;
    }

    /**
     * @param $type
     * @param $service
     * @param $requestParams
     *
     * @return mixed
     * @throws Exception
     */
    public function call($type, $service, $requestParams)
    {
        $soapClient = $this->create(
            $this->createWsdlUrl($service),
            $this->getOptionsArray()
        );

        $soapClient->__setSoapHeaders($this->getSoapHeader());
        try {
            return $soapClient->__call($type, [$requestParams]);
        } catch (Exception $e) {

            throw new Exception(
                __('Faild on soap call : %1', $e->getMessage()),
                0,
                Exception::HTTP_INTERNAL_ERROR
            );
        }
    }

    /**
     * @param $wsdlUrl
     * @param $options
     *
     * @return \SoapClient
     */
    public function create($wsdlUrl, $options)
    {
        $soapClient = new \SoapClient($wsdlUrl,  $options);
        return $soapClient;
    }

    /**
     * @param $service
     *
     * @return string $url
     */
    public function createWsdlUrl($service)
    {
        return self::TEST_SERVICE_WSDL_URL .  $service . '/?wsdl';
    }

    /**
     * @param string $uri
     * @param bool $isCif
     *
     * @return array
     */
    protected function getOptionsArray($uri = '', $isCif = true)
    {
        $options = [
            'uri'            => $uri,
            'soap_version'   => SOAP_1_1,
            'features'       => SOAP_SINGLE_ELEMENT_ARRAYS,
            'trace'          => true,
        ];

        if (!$isCif) {
            $options['stream_context'] = stream_context_create(
                ['http' => ['header' => 'apikey:'.$this->accountConfig->getApiKey()]]
            );
        }
        return $options;
    }

    /**
     * @todo is only needed for old CIF calls so can be removed once API is fixed.
     * @return mixed
     */
    protected function getSoapHeader()
    {
        $firstNode  = new \SoapVar(self::TEST_USERNAME, XSD_STRING, null, null, 'Username', self::HEADER_NAMESPACE);
        $secondNode = new \SoapVar(self::TEST_PASSWORD, XSD_STRING, null, null, 'Password', self::HEADER_NAMESPACE);
        $token      = new \SoapVar(
            [$firstNode, $secondNode], SOAP_ENC_OBJECT, null, null, 'usernameToken', self::HEADER_NAMESPACE
        );
        $security   = new \SoapVar([$token], SOAP_ENC_OBJECT, null, null, 'Security', self::HEADER_NAMESPACE);

        return new \SOAPHeader(self::HEADER_NAMESPACE, 'Security', $security, false);
    }

    /**
     * @throws Exception
     */
    protected function checkSoapExtensionIsLoaded()
    {
        if (!extension_loaded('soap')) {
            throw new Exception(
                // @codingStandardsIgnoreLine
                __('SOAP extension is not loaded.'),
                0,
                Exception::HTTP_INTERNAL_ERROR
            );
        }
    }
}
