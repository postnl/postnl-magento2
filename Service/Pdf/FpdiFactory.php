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
namespace TIG\PostNL\Service\Pdf;

use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Module\Manager;
use TIG\PostNL\Service\Shipment\Label\File;

/**
 * As Magento does auto generate the Fpdi class when using FpdiFactory we are doing this ourself.
 */
class FpdiFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var File
     */
    private $file;

    /**
     * @param ObjectManager $objectManager
     * @param Manager       $moduleManager
     * @param File          $file
     */
    public function __construct(
        ObjectManager $objectManager,
        Manager $moduleManager,
        File $file
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        if (!$this->moduleManager->isEnabled('Fooman_PrintOrderPdf')) {
            // @codingStandardsIgnoreLine
            return $this->objectManager->create(Fpdi::class, [
                'file' => $this->file,
                'orientation' => 'P',
                'unit' => 'mm',
                'size' => 'A4',
            ]);
        }

        return $this->constructTCPDF();
    }

    /**
     * @return mixed
     */
    private function constructTCPDF()
    {
        // @codingStandardsIgnoreLine
        return $this->objectManager->create(Fpdi::class, [
            'file' => File::class,
            'orientation' => 'P',
            'unit' => 'mm',
            'size' => 'A4',
            'unicode' => true,
            'encoding' => 'UTF-8',
            'diskcache' => false,
            'pdfa' => false,
        ]);
    }

    /**
     * @param $pdf
     *
     * @return string
     * @throws \Exception
     */
    public function saveFile($pdf)
    {
        $file = $this->file->save($pdf);

        return $file;
    }

    public function cleanupFiles()
    {
        $this->file->cleanup();
    }
}
