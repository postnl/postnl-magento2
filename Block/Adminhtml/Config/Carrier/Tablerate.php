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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Block\Adminhtml\Config\Carrier;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\OfflineShipping\Model\Config\Backend\Tablerate as MagentoTablerate;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\TablerateFactory as MagentoTablerateFactory;

use TIG\PostNL\Model\ResourceModel\TablerateFactory;

/**
 * Class TablerateExport
 *
 * @package TIG\PostNL\Block\Adminhtml\Config\Carrier
 */
class Tablerate extends MagentoTablerate
{
    /**
     * By overriding the parameters of the consturctor, the PostNL Tablerate resourcemodel
     * will be used instead the one of OfflineShipping.
     *
     * @param Context                 $context
     * @param Registry                $registry
     * @param ScopeConfigInterface    $config
     * @param TypeListInterface       $cacheTypeList
     * @param MagentoTablerateFactory $tablerateFactory
     * @param TablerateFactory        $postnlTablerateFactory
     * @param AbstractResource        $resource
     * @param AbstractDb              $resourceCollection
     * @param array                   $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        MagentoTablerateFactory $tablerateFactory,
        TablerateFactory $postnlTablerateFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $tablerateFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_tablerateFactory = $postnlTablerateFactory;
    }
}
