<?php

namespace MaintenanceToolboxBundle\Tool;

class ArrayFormatter
{
    /**
     * Convert flat array with structured keys into multidimensional array
     *
     * from ['a' => ['b' => ['c' => 'foo']]]
     * to ['a__b__c' => 'foo' ]
     *
     * @param array $data
     * @param string $prefix
     * @return array
     */
    public function toFlatArray(array $data, string $prefix = ''): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '__') . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->toFlatArray($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }
}
