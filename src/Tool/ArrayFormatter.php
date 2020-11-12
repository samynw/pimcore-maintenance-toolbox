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

    /**
     * Convert flat array with structured keys into multidimensional array
     *
     * from ['a__b__c' => 'foo' ]
     * to ['a' => ['b' => ['c' => 'foo']]]
     *
     * @param array $input
     * @return array
     */
    public function toNestedArray(array $input): array
    {
        $output = [];

        foreach ($input as $key => $value) {
            $this->setNestedOption($output, $key, $value);
        }

        return $output;
    }

    /**
     * Set a nested option:
     * - create new level on each __ in the key
     *
     * @param array $data
     * @param $key
     * @param $value
     */
    private function setNestedOption(array &$data, $key, $value): void
    {
        $keys = explode('__', $key);

        // Keep the last key aside to process as last
        $last_key = array_pop($keys);

        // For each key segment, create a new level in the array
        while ($segmentKey = array_shift($keys)) {
            if (!array_key_exists($segmentKey, $data)) {
                $data[$segmentKey] = [];
            }

            $data = &$data[$segmentKey];
        }

        // set the final key
        $data[$last_key] = $value;
    }
}
