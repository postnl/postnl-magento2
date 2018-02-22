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
namespace TIG\PostNL\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\Manager;
use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Source\LabelAndPackingslip\ReferenceType;
use TIG\PostNL\Service\Wrapper\StoreInterface;

/**
 * @codingStandardsIgnoreStart
 */
class LabelAndPackingslipOptions extends AbstractConfigProvider
{
    const XPATH_LABEL_PACKINGSLIP_OPTION_REFERENCE_TYPE   = 'tig_postnl/labelandpackingslipoptions/reference_type';
    const XPATH_LABEL_PACKINGSLIP_OPTION_CUSTOM_REFERENCE = 'tig_postnl/labelandpackingslipoptions/custom_shipment_reference';
    const XPATH_LABEL_PACKINGSLIP_OPTION_SHOW_LABEL       = 'tig_postnl/labelandpackingslipoptions/show_label';
    /**
     * @var StoreInterface
     */
    private $storeWrapper;

    /**
     * LabelAndPackingslipOptions constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager              $moduleManager
     * @param Encryptor            $crypt
     * @param StoreInterface       $storeWrapper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        Encryptor $crypt,
        StoreInterface $storeWrapper
    ) {
        parent::__construct($scopeConfig, $moduleManager, $crypt);
        $this->storeWrapper = $storeWrapper;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return string
     */
    public function getReference($shipment)
    {
        $reference = '';
        $referenceType = $this->getReferenceType();

        switch ($referenceType)
        {
            case ReferenceType::REFEENCE_TYPE_NONE:
                $reference = '';
                break;
            case ReferenceType::REFEENCE_TYPE_ORDER_ID:
                $reference = $shipment->getOrder()->getIncrementId();
                break;
            case ReferenceType::REFEENCE_TYPE_SHIPMENT_ID:
                $reference = $shipment->getIncrementId();
                break;
            case ReferenceType::REFEENCE_TYPE_CUSTOM:
                $reference = $this->getCustomReferenceParsed($shipment);
                break;
        }

        return $reference;
    }

    /**
     * @return string
     */
    public function getReferenceType()
    {
        return $this->getConfigFromXpath(self::XPATH_LABEL_PACKINGSLIP_OPTION_REFERENCE_TYPE);
    }

    /**
     * @return string
     */
    public function getCustomReference()
    {
        return $this->getConfigFromXpath(self::XPATH_LABEL_PACKINGSLIP_OPTION_CUSTOM_REFERENCE);
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return string
     */
    public function getCustomReferenceParsed($shipment)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeWrapper->getStore();
        $customReference = $this->getCustomReference();

        $customReference = str_replace('{{var shipment_increment_id}}', $shipment->getIncrementId(), $customReference);
        $customReference = str_replace('{{var order_increment_id}}', $shipment->getOrder()->getIncrementId(), $customReference);
        $customReference = str_replace('{{var store_frontend_name}}', $store->getFrontendName(), $customReference);

        return $customReference;
    }

    /**
     * @return string
     */
    public function getShowLabel()
    {
        return $this->getConfigFromXpath(self::XPATH_LABEL_PACKINGSLIP_OPTION_SHOW_LABEL);
    }
}