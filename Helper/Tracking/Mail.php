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
namespace TIG\PostNL\Helper\Tracking;

use TIG\PostNL\Helper\AbstractTracking;
use TIG\PostNL\Helper\Data as PostNLHelper;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Logging\Log;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Exception\MailException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Mail extends AbstractTracking
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var PostNLHelper
     */
    private $postNLHelperData;

    /**
     * @var TransportInterface
     */
    private $trackAndTraceEmail;

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @param Context                  $context
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param TransportBuilder         $transportBuilder
     * @param PostNLHelper             $data
     * @param Webshop                  $webshop
     * @param Log                      $logging
     * @param AssetRepository          $assetRepository
     */
    public function __construct(
        Context $context,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransportBuilder $transportBuilder,
        PostNLHelper $data,
        Webshop $webshop,
        Log $logging,
        AssetRepository $assetRepository
    ) {
        $this->transportBuilder     = $transportBuilder;
        $this->postNLHelperData     = $data;
        $this->assetRepository      = $assetRepository;

        parent::__construct(
            $context,
            $postNLShipmentRepository,
            $searchCriteriaBuilder,
            $webshop,
            $logging
        );
    }

    /**
     * @return void
     * @throws MailException
     */
    public function send()
    {
        try {
            $this->trackAndTraceEmail->sendMessage();
        } catch (MailException $exception) {
            $this->logging->addCritical($exception->getLogMessage());
        }
    }

    /**
     * @param Shipment $shipment
     * @param string $url
     *
     */
    // @codingStandardsIgnoreStart
    public function set($shipment, $url)
    {
        $template  = $this->webshopConfig->getTrackAndTraceEmailTemplate($shipment->getStoreId());
        $transport = $this->transportBuilder->setTemplateIdentifier($template);
        $transport->setTemplateOptions([
            'area'  => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
            'store' => $shipment->getStoreId()
        ]);

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $transport->setTemplateVars(
            $this->getTemplateVars($order, $url)
        );

        $transport->setFrom('general');
        $address = $shipment->getShippingAddress();
        $transport->addTo($address->getEmail(), $address->getFirstname() . ' '. $address->getLastname());
        $transport = $this->addBccEmail($transport);
        $this->logging->addInfo('Track And Trace email build for :'. $address->getEmail());
        $this->trackAndTraceEmail = $transport->getTransport();
    }
    // @codingStandardsIgnoreEnd

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param $url
     *
     * @return array
     */
    private function getTemplateVars($order, $url)
    {
        $shipment        = $this->postNLShipmentRepository->getByFieldWithValue('order_id', $order->getId());
        $shippingAddress = $shipment->getOriginalShippingAddress();
        $billingAddress  = $order->getBillingAddress();

        return [
            'order_id'        => $order,
            'postnlShipment'  => $shipment,
            'shippingAddress' => $shippingAddress,
            'billingAddress'  => $billingAddress,
            'dateAndTime'     => $this->postNLHelperData->getDate(),
            'url'             => $url,
            'logo_url'        => $this->getLogoUrl(),
            'address_type'    => $this->getAddressType($shipment),
            'name'            => $shippingAddress->getFirstname() . ' ' .
                $shippingAddress->getMiddlename() . ' ' .
                $shippingAddress->getLastname(),
            'street'          => $this->getStreetFlattend($shippingAddress->getStreet())
        ];
    }

    /**
     * @param $street
     *
     * @return string
     */
    private function getStreetFlattend($street)
    {
        if (!is_array($street)) {
            return $street;
        }

        return trim(implode(' ', $street));
    }

    /**
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $shipment
     *
     * @return \Magento\Framework\Phrase
     */
    private function getAddressType($shipment)
    {
        if ($shipment->getIsPakjegemak()) {
            // @codingStandardsIgnoreLine
            return __('Pakjegemak address');
        }

        // @codingStandardsIgnoreLine
        return __('Shipping address');
    }

    /**
     * Get the url to the logo
     *
     * @return string
     */
    private function getLogoUrl()
    {
        try {
            return $this->assetRepository->getUrlWithParams(
                'TIG_PostNL::images/postnl_logo.png',
                ['_secure' => true]
            );
        } catch (LocalizedException $exception) {
            $this->logging->critical($exception->getLogMessage(), $exception->getTrace());
            return 'https://www.postnl.nl/img/logo.png';
        }
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transport
     *
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     */
    private function addBccEmail($transport)
    {
        if ($this->webshopConfig->getTrackAndTraceBccEmail()) {
            $transport->addBcc($this->webshopConfig->getTrackAndTraceBccEmail());
        }

        return $transport;
    }
}
