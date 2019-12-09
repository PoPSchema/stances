<?php
namespace PoP\Stances\FieldResolvers;

use PoP\ComponentModel\Utils;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\FieldResolvers\AbstractFunctionalFieldResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\Stances\TypeResolvers\StanceTypeResolver;

class StanceFunctionalFieldResolver extends AbstractFunctionalFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(
            StanceTypeResolver::class,
        );
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'stance-name',
            'cat-name',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'stance-name' => SchemaDefinition::TYPE_STRING,
            'cat-name' => SchemaDefinition::TYPE_STRING,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'stance-name' => $translationAPI->__('', ''),
            'cat-name' => $translationAPI->__('', ''),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $stance = $resultItem;
        switch ($fieldName) {
            case 'stance-name':
            case 'cat-name':
                $selected = $typeResolver->resolveValue($resultItem, 'stance', $variables, $expressions, $options);
                $params = array(
                    'selected' => $selected
                );
                $stance = new GD_FormInput_Stance($params);
                return $stance->getSelectedValue();
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
