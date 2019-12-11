<?php
namespace PoP\Stances\TypeResolvers;

use PoP\Posts\TypeResolvers\PostTypeResolver;
use PoP\Stances\TypeDataResolvers\StanceTypeDataResolver;

class StanceTypeResolver extends PostTypeResolver
{
    public function getTypeDataResolverClass(): string
    {
        return StanceTypeDataResolver::class;
    }
}

