<?php
namespace PoP\Stances\TypeResolverPickers;

use PoP\ComponentModel\TypeResolverPickers\AbstractTypeResolverPicker;
use PoP\Posts\TypeResolvers\PostUnionTypeResolver;
use PoP\Stances\TypeResolvers\StanceTypeResolver;

class StanceTypeResolverPicker extends AbstractTypeResolverPicker
{
    public static function getClassesToAttachTo(): array
    {
        return [
            PostUnionTypeResolver::class,
        ];
    }

    public function getSchemaDefinitionObjectNature(): string
    {
        return 'is-userstance';
    }

    public function getTypeResolverClass(): string
    {
        return StanceTypeResolver::class;
    }

    public function process($resultItemOrID): bool
    {
        $cmspostsapi = \PoP\Posts\FunctionAPIFactory::getInstance();
        $cmspostsresolver = \PoP\Posts\ObjectPropertyResolverFactory::getInstance();
        $postID = is_object($resultItemOrID) ? $cmspostsresolver->getPostId($resultItemOrID) : $resultItemOrID;
        return $cmspostsapi->getPostType($postID) == POP_USERSTANCE_POSTTYPE_USERSTANCE;
    }
}
