<?php

namespace TIG\PostNL\Service\Order\Compatibility;

use Magento\Framework\ObjectManagerInterface;
use Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySkuFactory as GetSourceItemsDataBySku;

/**
 * Not the most elegant fix but when the Magento Inventory extensions are enabled, the default inventory should be
 * retrieved. This is done with a feature that exists within the Magento_InventoryCatalogAdminUi module.
 *
 * There are two cases that could go wrong.
 * 1. We don't have the SourceItemsDataBySkuProxy in the constructor.
 * Magento won't compile the class in setup:di:compile. Magento will try to create it on the fly. This will cause issues
 * when the generated folder doesn't have write access (e.g. Magento Cloud solutions)
 *
 * 2. We do have the SourceItemsDataBySkuProxy in the constructor.
 * Magento will try to compile the class in setup:di:compile. When the environment doesn't have the Magento Inventory
 * extensions removed, the following error appears:
 * "Class Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySkuFactory\Proxy does not exist"
 *
 * The following solution will create a near empty class when the SourceItemsDataBySkuProxy is created.
 */
// @codingStandardsIgnoreFile

if (class_exists(GetSourceItemsDataBySku::class)):
    class SourceItemsDataBySkuProxy
    {

        /**
         * @var ObjectManagerInterface
         */
        private $objectManager;

        /**
         * @var GetSourceItemsDataBySku
         */
        private $subject;

        /**
         * @param ObjectManagerInterface  $objectManager
         * @param GetSourceItemsDataBySku $sourceItemsDataBySku
         */
        public function __construct(
            ObjectManagerInterface $objectManager,
            GetSourceItemsDataBySku $sourceItemsDataBySku
        ) {
            $this->objectManager = $objectManager;
        }

        /**
         * @return GetSourceItemsDataBySku
         */
        private function getSubject()
        {
            if (!$this->subject) {
                $this->subject = $this->objectManager->get(GetSourceItemsDataBySku::class);
            }
            return $this->subject;
        }

        /**
         * @param array $data
         *
         * @return GetSourceItemsDataBySku
         */
        public function create(array $data = [])
        {
            return $this->getSubject()->create($data);
        }
    }
else:
    // This class should realistically never be reached outside the setup:di:compile
    class SourceItemsDataBySkuProxy
    {
        public function create(array $data = [])
        {
            return false;
        }
    }
endif;
