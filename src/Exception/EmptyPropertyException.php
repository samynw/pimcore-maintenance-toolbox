<?php

namespace MaintenanceToolboxBundle\Exception;

class EmptyPropertyException extends \RuntimeException
{
    /**
     * Generate an EmptyPropertyException based on the property name
     * @param string $property
     * @return EmptyPropertyException
     */
    public static function forProperty(string $property): EmptyPropertyException
    {
        return new self(sprintf(
            'The value of %s was expected to be found but was empty',
            $property
        ));
    }
}
