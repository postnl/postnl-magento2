<?php

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
