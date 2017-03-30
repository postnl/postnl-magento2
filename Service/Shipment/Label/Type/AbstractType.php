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
namespace TIG\PostNL\Service\Shipment\Label\Type;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Shipment\Label\File;
use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;

abstract class AbstractType
{
    /**
     * @var Fpdi
     */
    // @codingStandardsIgnoreLine
    protected $pdf;

    /**
     * @var File
     */
    private $file;

    /**
     * @param FpdiFactory $Fpdi
     * @param File        $file
     */
    public function __construct(
        FpdiFactory $Fpdi,
        File $file
    ) {
        $this->pdf = $Fpdi->create();
        $this->file = $file;
    }

    /**
     * Fpdi expects the labels to be provided as files, therefore temporarily save each label in the var folder.
     *
     * @param ShipmentLabelInterface $label
     *
     * @return string
     */
    public function saveTempLabel(ShipmentLabelInterface $label)
    {
        /**
         * Decode the label received from PostNL.
         */
        // @codingStandardsIgnoreLine
        return $this->file->save(base64_decode($label->getLabel()));
    }

    /**
     * Delete the file generated in the previous step.
     */
    public function cleanup()
    {
        $this->file->cleanup();
    }
}
