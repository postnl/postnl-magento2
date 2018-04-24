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
namespace TIG\PostNL\Service\Shipment\Label;

class Generate
{
    /**
     * @var Prepare
     */
    private $prepare;

    /**
     * @var Merge
     */
    private $merge;

    /**
     * @param Prepare $prepare
     * @param Merge   $merge
     */
    public function __construct(
        Prepare $prepare,
        Merge $merge
    ) {
        $this->prepare = $prepare;
        $this->merge = $merge;
    }

    /**
     *
     *
     * @param array $labels
     * @codingStandardsIgnoreStart
     * @param bool  $createNewPdf Sometimes you want to generate a new Label PDF, for example when printing packingslips
     *                            This parameter indicates whether to reuse the existing label PDF
     *                            @TODO Refactor to a cleaner way rather than chaining all the way to \TIG\PostNL\Service\Shipment\Label\Merge\AbstractMerger
     * @codingStandardsIgnoreEnd
     *
     * @return string
     */
    public function run(array $labels, $createNewPdf = false)
    {
        $preparedLabels = [];
        foreach ($labels as $label) {
            $preparedLabels[] = $this->prepare->label($label);
        }

        return $this->merge->files($preparedLabels, $createNewPdf);
    }
}
