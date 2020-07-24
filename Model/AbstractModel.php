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
namespace TIG\PostNL\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel as AbstractBasicModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;

class AbstractModel extends AbstractBasicModel
{
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->dateTime = $dateTime;
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $this->changeUpdatedAt($this->dateTime->gmtDate());

        if (!$this->getCreatedAt()) {
            $this->changeCreatedAt($this->dateTime->gmtDate());
        }

        return parent::beforeSave();
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function changeCreatedAt($value)
    {
        return $this->setData(static::FIELD_CREATED_AT, $value);
    }

    /**
     * @return null|string
     */
    public function getCreatedAt()
    {
        return $this->getData(static::FIELD_CREATED_AT);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function changeUpdatedAt($value)
    {
        return $this->setData(static::FIELD_UPDATED_AT, $value);
    }

    /**
     * @return null|string
     */
    public function getUpdatedAt()
    {
        return $this->getData(static::FIELD_UPDATED_AT);
    }
}
