<?php
namespace PoP\Stances\TypeResolvers;

use PoP\Posts\TypeResolvers\PostTypeResolver;
use PoP\Stances\TypeDataLoaders\StanceTypeDataLoader;

class StanceTypeResolver extends PostTypeResolver
{
    public function getTypeDataLoaderClass(): string
    {
        return StanceTypeDataLoader::class;
    }
}

