<?php

namespace TIG\PostNL\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\Manager;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order;
use TIG\PostNL\Config\Source\LabelAndPackingslip\ReferenceType;
use TIG\PostNL\Service\Wrapper\StoreInterface;
use TIG\PostNL\Config\Source\Options\ProductOptions;

class LabelAndPackingslipOptions extends AbstractConfigProvider
{
    const XPATH_LABEL_PACKINGSLIP_REFERENCE_TYPE   = 'tig_postnl/labelandpackingslipoptions/reference_type';
    const XPATH_LABEL_PACKINGSLIP_CUSTOM_REFERENCE = 'tig_postnl/labelandpackingslipoptions/custom_shipment_reference';
    const XPATH_LABEL_PACKINGSLIP_SHOW_LABEL       = 'tig_postnl/labelandpackingslipoptions/show_label';
    const XPATH_LABEL_PACKINGSLIP_CALCULATE_LABELS = 'tig_postnl/labelandpackingslipoptions/calculate_labels';
    // @codingStandardsIgnoreLine
    const XPATH_LABEL_PACKINGSLIP_MAX_WEIGHT       = 'tig_postnl/labelandpackingslipoptions/calculate_labels_max_weight';

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
     * @param ProductOptions       $productOptions,
     * @param StoreInterface       $storeWrapper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        Encryptor $crypt,
        ProductOptions $productOptions,
        StoreInterface $storeWrapper
    ) {
        parent::__construct($scopeConfig, $moduleManager, $crypt, $productOptions);
        $this->storeWrapper = $storeWrapper;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return string
     */
    public function getReference($shipment)
    {
        $referenceType = $this->getReferenceType();

        switch ($referenceType) {
            case ReferenceType::REFEENCE_TYPE_ORDER_ID:
                /** @var Order $order */
                $order = $shipment->getOrder();
                return $order->getIncrementId();
            case ReferenceType::REFEENCE_TYPE_SHIPMENT_ID:
                return $shipment->getIncrementId();
            case ReferenceType::REFEENCE_TYPE_CUSTOM:
                return $this->getCustomReferenceParsed($shipment);
            case ReferenceType::REFEENCE_TYPE_NONE:
            default:
                return '';
        }
    }

    /**
     * @return string
     */
    public function getReferenceType()
    {
        return $this->getConfigFromXpath(self::XPATH_LABEL_PACKINGSLIP_REFERENCE_TYPE);
    }

    /**
     * @return string
     */
    public function getCustomReference()
    {
        return $this->getConfigFromXpath(self::XPATH_LABEL_PACKINGSLIP_CUSTOM_REFERENCE);
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
        /** @var Order $order */
        $order = $shipment->getOrder();

        $customReference = str_replace('{{var shipment_increment_id}}', $shipment->getIncrementId(), $customReference);
        $customReference = str_replace('{{var order_increment_id}}', $order->getIncrementId(), $customReference);
        $customReference = str_replace('{{var store_frontend_name}}', $store->getFrontendName(), $customReference);

        return $customReference;
    }

    /**
     * @return string
     */
    public function getShowLabel()
    {
        return $this->getConfigFromXpath(self::XPATH_LABEL_PACKINGSLIP_SHOW_LABEL);
    }

    /**
     * @return string
     */
    public function getCalculateLabels()
    {
        return $this->getConfigFromXpath(self::XPATH_LABEL_PACKINGSLIP_CALCULATE_LABELS);
    }

    /**
     * @return int
     */
    public function getCalculateLabelsMaxWeight()
    {
        return $this->getConfigFromXpath(self::XPATH_LABEL_PACKINGSLIP_MAX_WEIGHT);
    }
}
