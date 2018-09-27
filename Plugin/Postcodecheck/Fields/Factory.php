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
namespace TIG\PostNL\Plugin\Postcodecheck\Fields;

use TIG\PostNL\Exception as PostNLException;

class Factory
{
    /**
     * @var FieldInterface[]
     */
    private $fields;

    /**
     * Factory constructor.
     *
     * @param array $fields
     */
    public function __construct(
        $fields = []
    ) {
        $this->fields = $fields;
    }

    /**
     * @param $type
     * @param $scope
     *
     * @return array
     */
    public function get($type, $scope)
    {
        foreach ($this->fields as $field) {
            $this->checkImplementation($field);
        }

        return $this->renderField($type, $scope);
    }

    /**
     * @param $type
     * @param $scope
     *
     * @return array
     * @throws PostNLException
     */
    private function renderField($type, $scope)
    {
        if (!isset($this->fields[$type])) {
            // @codingStandardsIgnoreLine
            throw new PostNLException(__('Could not find type %1 as postcodecheck field', $type));
        }

        return $this->fields[$type]->get($scope);
    }

    /**
     * @param $field
     *
     * @throws PostNLException
     */
    private function checkImplementation($field)
    {
        if (!array_key_exists(FieldInterface::class, class_implements($field))) {
            // @codingStandardsIgnoreLine
            throw new PostNLException(__('Class is not an implementation of %1', FieldInterface::class));
        }
    }
}
