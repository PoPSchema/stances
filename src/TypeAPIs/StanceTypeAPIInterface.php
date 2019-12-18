<?php
namespace PoP\Stances\TypeAPIs;

/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
interface StanceTypeAPIInterface
{
    /**
     * Indicates if the passed object is of type Stance
     *
     * @param [type] $object
     * @return boolean
     */
    public function isInstanceOfStanceType($object): bool;
}
