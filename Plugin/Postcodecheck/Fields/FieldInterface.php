<?php

namespace TIG\PostNL\Plugin\Postcodecheck\Fields;

interface FieldInterface
{
    /**
     * @param string $scope
     *
     * @return array
     */
    public function get($scope);
}
