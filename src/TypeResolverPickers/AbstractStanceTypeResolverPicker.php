<?php
namespace PoP\Stances\TypeResolverPickers;

use PoP\Stances\Facades\StanceTypeAPIFacade;
use PoP\Stances\TypeResolvers\StanceTypeResolver;
use PoP\ComponentModel\TypeResolverPickers\AbstractTypeResolverPicker;

class AbstractStanceTypeResolverPicker extends AbstractTypeResolverPicker
{
    public function getTypeResolverClass(): string
    {
        return StanceTypeResolver::class;
    }

    public function isInstanceOfType($object): bool
    {
        $stanceTypeAPI = StanceTypeAPIFacade::getInstance();
        return $stanceTypeAPI->isInstanceOfStanceType($object);
    }

    public function isIDOfType($resultItemID): bool
    {
        $stanceTypeAPI = StanceTypeAPIFacade::getInstance();
        return $stanceTypeAPI->stanceExists($resultItemID);
    }
}
