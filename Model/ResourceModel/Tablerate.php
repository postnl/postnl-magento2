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
namespace TIG\PostNL\Model\ResourceModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Import;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQueryFactory;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

use TIG\PostNL\Model\Carrier\Tablerate as CarrierTablerate;

/**
 * Class Tablerate
 *
 * @package TIG\PostNL\Model\ResourceModel
 */
class Tablerate extends \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
{
    /**
     * Tablerate constructor.
     * By overriding the parameters of the consturctor,
     * the PostNL Tablerate model will be used instead the one of OfflineShipping.
     *
     * @param Context               $context
     * @param LoggerInterface       $logger
     * @param ScopeConfigInterface  $coreConfig
     * @param StoreManagerInterface $storeManager
     * @param CarrierTablerate      $carrierTablerate
     * @param Filesystem            $filesystem
     * @param Import                $import
     * @param RateQueryFactory      $rateQueryFactory
     * @param null                  $connectionName
     */
    // @codingStandardsIgnoreStart
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        ScopeConfigInterface $coreConfig,
        StoreManagerInterface $storeManager,
        CarrierTablerate $carrierTablerate,
        Filesystem $filesystem,
        Import $import,
        RateQueryFactory $rateQueryFactory,
        $connectionName = null
    ) {
        parent::__construct(
            $context,
            $logger,
            $coreConfig,
            $storeManager,
            $carrierTablerate,
            $filesystem,
            $import,
            $rateQueryFactory,
            $connectionName
        );
    }
    // @codingStandardsIgnoreEnd

    /**
     * Constructor defining the resource model table and primary key
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('tig_postnl_tablerate', 'entity_id');
    }

    /**
     * The parent method has lines that always refer to the uploaded files of OfflineShipping.
     * By overriding the method, the correct uploaded file can be refered and imported.
     *
     * {@inheritdoc}
     */
    public function uploadAndImport(DataObject $object)
    {
        // @codingStandardsIgnoreLine
        if (empty($_FILES['groups']['tmp_name']['tig_postnl']['fields']['import']['value'])) {
            return $this;
        }

        // @codingStandardsIgnoreLine
        $tablerateFile = $_FILES['groups']['tmp_name']['tablerate']['fields']['import']['value'];
        // @codingStandardsIgnoreLine
        $postnlFile = $_FILES['groups']['tmp_name']['tig_postnl']['fields']['import']['value'];

        // @codingStandardsIgnoreLine
        $_FILES['groups']['tmp_name']['tablerate']['fields']['import']['value'] = $postnlFile;

        parent::uploadAndImport($object);

        // @codingStandardsIgnoreLine
        $_FILES['groups']['tmp_name']['tablerate']['fields']['import']['value'] = $tablerateFile;
    }

    /**
     * The parent method has lines that always refer to the configuration values of OfflineShipping.
     * By overriding the method, the configuration values of PostNL will be used.
     *
     * {@inheritdoc}
     */
    public function getConditionName(DataObject $object)
    {
        $conditionName = $object->getData('groups/tig_postnl/fields/condition_name/value');

        if ($object->getData('groups/tig_postnl/fields/condition_name/inherit') == '1') {
            $conditionName = (string)$this->coreConfig->getValue('carriers/tig_postnl/condition_name', 'default');
        }

        return $conditionName;
    }
}
