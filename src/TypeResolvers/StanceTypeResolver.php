<?php
namespace PoP\Stances\TypeResolvers;

use PoP\Stances\Facades\StanceTypeAPIFacade;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\Stances\TypeDataLoaders\StanceTypeDataLoader;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

class StanceTypeResolver extends AbstractTypeResolver
{
	public const NAME = 'Stance';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('A stance by the user (from among “positive”, “neutral” or “negative”) and why', 'stances');
    }

    public function getId($resultItem)
    {
        $stanceTypeAPI = StanceTypeAPIFacade::getInstance();
        return $stanceTypeAPI->getID($resultItem);
    }

    public function getTypeDataLoaderClass(): string
    {
        return StanceTypeDataLoader::class;
    }
}

