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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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

namespace TIG\PostNL\Controller\Adminhtml\Order;

use Laminas\Mime\Mime;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use TIG\PostNL\Config\Provider\ReturnOptions;

class Email
{
    /** @var ScopeConfigInterface  */
    private $scopeConfig;

    /** @var StoreManagerInterface  */
    private $storeManager;

    /** @var TransportBuilder  */
    private $transportBuilder;

    /** @var LoggerInterface  */
    private $logger;

    /** @var StateInterface  */
    private $state;

    /** @var ReturnOptions  */
    private $returnOptions;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder      $transportBuilder
     * @param LoggerInterface       $logger
     * @param StateInterface        $state
     * @param ReturnOptions         $returnOptions
     */
    public function __construct(
        ScopeConfigInterface  $scopeConfig,
        StoreManagerInterface $storeManager,
        TransportBuilder      $transportBuilder,
        LoggerInterface       $logger,
        StateInterface        $state,
        ReturnOptions         $returnOptions
    ) {
        $this->scopeConfig      = $scopeConfig;
        $this->storeManager     = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->logger           = $logger;
        $this->state            = $state;
        $this->returnOptions    = $returnOptions;
    }

    /**
     * @param $shipment
     * @param $labels
     *
     * @return void
     */
    public function sendEmail($shipment, $labels)
    {
        $shippingAddress = $shipment->getShippingAddress();
        $templateId      = $this->returnOptions->getEmailTemplate();
        $fileName        = 'SmartReturnLabel_' . $shipment->getIncrementId() . '.pdf';
        $fromEmail       = $this->scopeConfig->getValue('trans_email/ident_sales/email');
        $fromName        = $this->scopeConfig->getValue('trans_email/ident_sales/name');
        $toEmail         = $shippingAddress->getEmail();
        $fileContent     = $this->getLabel($labels);

        try {
            $templateVars = [
                'name'           => $shippingAddress->getName(),
                'email'          => $toEmail,
                'ordernumber'    => $shipment->getOrder()->getIncrementId(),
                'shipmentnumber' => $shipment->getIncrementId()
            ];

            $storeId = $this->storeManager->getStore()->getId();
            $from    = ['email' => $fromEmail, 'name' => $fromName];
            $this->state->suspend();

            $templateOptions = [
                'area'  => Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                                                ->setTemplateOptions($templateOptions)
                                                ->setTemplateVars($templateVars)
                                                ->setFrom($from)
                                                ->addAttachment(
                                                    base64_decode($fileContent),
                                                    'text/pdf',
                                                    Mime::DISPOSITION_ATTACHMENT,
                                                    Mime::ENCODING_BASE64,
                                                    $fileName
                                                )
                                                ->addTo($toEmail)
                                                ->getTransport();
            $transport->sendMessage();
            $this->state->resume();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param $labels
     *
     * @return mixed
     */
    public function getLabel($labels)
    {
        foreach ($labels as $key => $label) {
            if ($label->getProductCode() === '2285' && $label->getReturnLabel() !== '1') {
                $returnLabels[$key] = $label->getEntityId();
            }
        }
        $key = array_keys($returnLabels, max($returnLabels))[0];

        return $labels[$key]->getLabel();
    }
}
