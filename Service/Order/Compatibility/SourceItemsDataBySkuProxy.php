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
namespace TIG\PostNL\Service\Order\Compatibility;

use Magento\Framework\ObjectManagerInterface;
use Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySkuFactory\Proxy as GetSourceItemsDataBySku;

// @codingStandardsIgnoreFile
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
     * @var GetSourceItemsDataBySku
     */
    private $proxy;

    /**
     * @param ObjectManagerInterface  $objectManager
     * @param GetSourceItemsDataBySku $proxy
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        GetSourceItemsDataBySku $proxy
    ) {
        $this->objectManager = $objectManager;
        $this->proxy = $proxy;
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
