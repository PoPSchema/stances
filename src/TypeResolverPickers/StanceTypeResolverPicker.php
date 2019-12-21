<?php
namespace PoP\Stances\TypeResolverPickers;

use PoP\Stances\Facades\StanceTypeAPIFacade;
use PoP\Stances\TypeResolvers\StanceTypeResolver;
use PoP\Posts\TypeResolvers\ContentEntityUnionTypeResolver;
use PoP\ComponentModel\TypeResolverPickers\AbstractTypeResolverPicker;

class StanceTypeResolverPicker extends AbstractTypeResolverPicker
{
    public static function getClassesToAttachTo(): array
    {
        return [
            ContentEntityUnionTypeResolver::class,
        ];
    }

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
