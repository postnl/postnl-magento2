<?php

namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;
use Magento\Sales\Api\OrderRepositoryInterface;
use TIG\PostNL\Api\OrderRepositoryInterface as PostNLOrderRepository;

// @codingStandardsIgnoreFile
class ProductOptions
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var GuaranteedOptions
     */
    private $guaranteedOptions;

    /**
     * @var ProductOptionsConfiguration
     */
    private $productOptionsConfig;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PostNLOrderRepository
     */
    private $postNLOrderRepository;

    /**
     * These shipment types need specific product options.
     *
     * @var array
     */
    private $availableProductOptions = [
            'pge'     => [
                [
                    'Characteristic' => '118',
                    'Option'         => '002',
                ]
            ],
            'evening' => [
                [
                    'Characteristic' => '118',
                    'Option'         => '006',
                ]
            ],
            'sunday'  => [
                [
                    'Characteristic' => '101',
                    'Option'         => '008',
                ]
            ],
            'idcheck' => [
                [
                    'Characteristic' => '002',
                    'Option'         => '014'
                ]
            ],
            'idcheck_pg' => [
                [
                    'Characteristic' => '002',
                    'Option'         => '014'
                ]
            ],
            'today' => [
                [
                    'Characteristic' => '118',
                    'Option'         => '044'
                ]
            ],
            'eps-1' => [
                [
                    'Characteristic' => '005',
                    'Option'         => '025'
                ],
                [
                    'Characteristic' => '101',
                    'Option'         => '012'
                ]
            ],
            'eps-2' => [
                [
                    'Characteristic' => '004',
                    'Option'         => '015'
                ],
                [
                    'Characteristic' => '101',
                    'Option'         => '012'
                ]
            ],
            'eps-3' => [
                [
                    'Characteristic' => '004',
                    'Option'         => '016'
                ],
                [
                    'Characteristic' => '101',
                    'Option'         => '012'
                ]
            ],
            'eps-4' => [
                [
                    'Characteristic' => '005',
                    'Option'         => '025'
                ],
                [
                    'Characteristic' => '101',
                    'Option'         => '013'
                ]
            ],
            'eps-5' => [
                [
                    'Characteristic' => '004',
                    'Option'         => '015'
                ],
                [
                    'Characteristic' => '101',
                    'Option'         => '013'
                ]
            ],
            'eps-6' => [
                [
                    'Characteristic' => '004',
                    'Option'         => '016'
                ],
                [
                    'Characteristic' => '101',
                    'Option'         => '013'
                ]
            ],
            'gp-1' => [
                [
                    'Characteristic' => '005',
                    'Option'         => '025'
                ]
            ],
            'gp-2' => [
                [
                    'Characteristic' => '004',
                    'Option'         => '015'
                ]
            ],
            'gp-3' => [
                [
                    'Characteristic' => '004',
                    'Option'         => '016'
                ]
            ]
        ];

    /**
     * ProductOptions constructor.
     *
     * @param ShippingOptions              $shippingOptions
     * @param GuaranteedOptions            $guaranteedOptions
     * @param ProductOptionsConfiguration  $productOptions
     * @param OrderRepositoryInterface     $orderRepository
     * @param PostNLOrderRepository        $postNLOrderRepository
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        GuaranteedOptions $guaranteedOptions,
        ProductOptionsConfiguration $productOptions,
        OrderRepositoryInterface $orderRepository,
        PostNLOrderRepository $postNLOrderRepository
    ) {
        $this->shippingOptions       = $shippingOptions;
        $this->guaranteedOptions     = $guaranteedOptions;
        $this->productOptionsConfig  = $productOptions;
        $this->orderRepository       = $orderRepository;
        $this->postNLOrderRepository = $postNLOrderRepository;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return array|bool
     */
    public function get($shipment)
    {
        if ($shipment->getAcInformation() && $shipment->getAcInformation()[0]['Characteristic'] != '000') {
            return $shipment->getAcInformation();
        }

        $acOptions = $this->getAcOptionsByOrderWithShipment($shipment);
        if (!$acOptions) {
            $acOptions = $this->getByShipment($shipment);
        }

        if ($acOptions && $acOptions[0]['Characteristic'] !== '000') {
            return $acOptions;
        }

        return false;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return array|mixed|null
     */
    public function getByShipment(ShipmentInterface $shipment)
    {
        $type = strtolower($shipment->getShipmentType());

        if (strlen($shipment->getProductCode()) > 4) {
            $type .= '-' . substr($shipment->getProductCode(), 0, 1);
        }

        if ($shipment->isIDCheck()) {
            $type = 'idcheck';
        }

        if (!array_key_exists($type, $this->availableProductOptions)) {
            return $this->checkGuaranteedOptions($shipment);
        }

        return $this->availableProductOptions[$type];
    }

    /**
     * @param $type
     *
     * @return array|null
     */
    public function getByType($type)
    {
        if (!array_key_exists($type, $this->availableProductOptions)) {
            return $this->guaranteedOptions->get($type);
        }

        return $this->availableProductOptions[$type];
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return array|bool
     */
    private function getAcOptionsByOrderWithShipment(ShipmentInterface $shipment)
    {
        $order = $this->postNLOrderRepository->getByOrderId($shipment->getOrderId());
        if (!$order) {
            return false;
        }

        if (!$order->getAcInformation()) {
            return false;
        }

        return [$order->getAcInformation()];
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return null|array
     */
    private function checkGuaranteedOptions($shipment)
    {
        if (!$this->shippingOptions->isGuaranteedDeliveryActive()) {
            return null;
        }

        $code = $shipment->getProductCode();
        if (!$this->productOptionsConfig->checkProductByFlags($code, 'isGuaranteedDelivery', true)) {
            return null;
        }

        $order = $this->orderRepository->get($shipment->getOrderId());
        $guaranteedTime = $this->productOptionsConfig->getGuaranteedDeliveryType(
            $this->isAlternative($order->getBaseGrandTotal()),
            $this->productOptionsConfig->getGuaranteedType($code)
        );

        return $this->guaranteedOptions->get($guaranteedTime);
    }

    /**
     * @param $totalAmount
     *
     * @return bool
     */
    private function isAlternative($totalAmount)
    {
        $alternativeActive = $this->productOptionsConfig->getUseAlternativeDefault();
        if (!$alternativeActive) {
            return false;
        }

        $alternativeMinAmount = $this->productOptionsConfig->getAlternativeDefaultMinAmount();
        if ($totalAmount >= $alternativeMinAmount) {
            return false;
        }

        return true;
    }
}
