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
namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Soap;

class Barcode extends AbstractEndpoint
{
    /**
     * @var Soap
     */
    protected $soap;

    /**
     * @var string
     */
    protected $version = 'v1_1';

    /**
     * @var string
     */
    protected $endpoint = 'barcode';

    /**
     * @param Soap $soap
     */
    public function __construct(
        Soap $soap
    ) {
        $this->soap = $soap;
    }

    /**
     * {@inheritDoc}
     */
    public function call()
    {
        $this->soap->call($this, 'GenerateBarcode', [
            'Message'  => [
                'MessageID' => 'bc546cd1b0cb67ba52bce49aefb3f9c1',
                'MessageTimeStamp' => '19-12-2016 08:43:31',
            ],
            'Customer' => [
                'CustomerCode' => 'TOTA',
                'CustomerNumber' => '11223344',
            ],
            'Barcode'  => array(
                'Type'  => '3S',
                'Range' => 'TOTA',
                'Serie' => '000000000-999999999',
            ),
        ]);
    }

    /**
     * @return string
     */
    public function getWsdlUrl()
    {
        return 'BarcodeWebService/1_1/';
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }
}
