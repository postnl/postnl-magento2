<?php
declare(strict_types=1);

namespace TIG\PostNL\Config\Model\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

class DeliveryDateOff extends Value
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param SerializerInterface $serializerInterface
     * @param Random $mathRandom
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context              $context,
        Registry             $registry,
        ScopeConfigInterface $config,
        TypeListInterface    $cacheTypeList,
        SerializerInterface  $serializerInterface,
        Random               $mathRandom,
        AbstractResource     $resource = null,
        AbstractDb           $resourceCollection = null,
        array                $data = []
    ) {
        $this->serializer = $serializerInterface;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @param array $value
     * @return array
     * @throws LocalizedException
     */
    protected function encodeArrayFieldValue(array $value): array
    {
        $result = [];
        foreach ($value as $date) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = ['delivery_date_off' => $date];
        }
        return $result;
    }

    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function _afterLoad(): void
    {
        $value = $this->getValue();
        if ($value) {
            $value = $this->serializer->unserialize($value);
            $value = $this->encodeArrayFieldValue($value);
        }
        $this->setValue($value);
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $date = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            $date[] = $row['delivery_date_off'];
        }
        $date = ($date) ? $this->serializer->serialize($date) : '';
        $this->setValue($date);

        return parent::beforeSave();
    }
}
