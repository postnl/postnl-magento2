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
namespace TIG\PostNL\Service\Shipment\Labelling\Handler;

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\File;

abstract class AbstractHandler
{
    /**
     * @var Fpdi
     */
    // @codingStandardsIgnoreLine
    protected $pdf;

    /**
     * @var File
     */
    // @codingStandardsIgnoreLine
    protected $file;

    /**
     * @var FpdiFactory
     */
    // @codingStandardsIgnoreLine
    protected $fpdiFactory;

    /**
     * @param FpdiFactory $fpdiFactory
     * @param File $file
     */
    public function __construct(
        FpdiFactory $fpdiFactory,
        File $file
    ) {
        $this->fpdiFactory = $fpdiFactory;
        $this->file = $file;
    }

    /**
     * @param $labelItem
     *
     * @return string
     */
    public function getTempLabel($labelItem)
    {
        /**
         * Decode the label received from PostNL.
         */
        // @codingStandardsIgnoreLine
        return $this->file->save($labelItem);
    }

    /**
     * Delete the file generated in the previous step.
     */
    public function cleanup()
    {
        $this->file->cleanup();
    }
}
