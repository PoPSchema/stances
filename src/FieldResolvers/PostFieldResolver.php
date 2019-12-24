<?php
namespace PoP\Stances\FieldResolvers;

use PoP\Posts\Facades\PostTypeAPIFacade;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Stances\TypeResolvers\StanceTypeResolver;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\LooseContracts\Facades\NameResolverFacade;
use PoP\ComponentModel\TypeResolvers\UnionTypeHelpers;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\Content\TypeResolvers\ContentEntityUnionTypeResolver;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\Content\FieldInterfaces\ContentEntityFieldInterfaceResolver;

class PostFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return [
            ContentEntityFieldInterfaceResolver::class,
        ];
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'cats',
            'cat-slugs',
            'stance',
            'title',
            'excerpt',
            'content',
            'stancetarget',
            'has-stancetarget',
            'stances',
            'has-stances',
            'stance-pro-count',
            'stance-neutral-count',
            'stance-against-count',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'cats' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'cat-slugs' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_STRING),
            'stance' => SchemaDefinition::TYPE_INT,
            'title' => SchemaDefinition::TYPE_STRING,
            'excerpt' => SchemaDefinition::TYPE_STRING,
            'content' => SchemaDefinition::TYPE_STRING,
            'stancetarget' => SchemaDefinition::TYPE_ID,
            'has-stancetarget' => SchemaDefinition::TYPE_BOOL,
            'stances' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'has-stances' => SchemaDefinition::TYPE_BOOL,
            'stance-pro-count' => SchemaDefinition::TYPE_INT,
            'stance-neutral-count' => SchemaDefinition::TYPE_INT,
            'stance-against-count' => SchemaDefinition::TYPE_INT,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'cats' => $translationAPI->__('', ''),
            'cat-slugs' => $translationAPI->__('', ''),
            'stance' => $translationAPI->__('', ''),
            'title' => $translationAPI->__('', ''),
            'excerpt' => $translationAPI->__('', ''),
            'content' => $translationAPI->__('', ''),
            'stancetarget' => $translationAPI->__('', ''),
            'has-stancetarget' => $translationAPI->__('', ''),
            'stances' => $translationAPI->__('', ''),
            'has-stances' => $translationAPI->__('', ''),
            'stance-pro-count' => $translationAPI->__('', ''),
            'stance-neutral-count' => $translationAPI->__('', ''),
            'stance-against-count' => $translationAPI->__('', ''),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $postTypeAPI = PostTypeAPIFacade::getInstance();
        $taxonomyapi = \PoP\Taxonomies\FunctionAPIFactory::getInstance();
        $stance = $resultItem;
        switch ($fieldName) {
            case 'cats':
                return $taxonomyapi->getPostTaxonomyTerms(
                    $typeResolver->getID($stance),
                    POP_USERSTANCE_TAXONOMY_STANCE,
                    [
                        'return-type' => POP_RETURNTYPE_IDS,
                    ]
                );

            case 'cat-slugs':
                return $taxonomyapi->getPostTaxonomyTerms(
                    $typeResolver->getID($stance),
                    POP_USERSTANCE_TAXONOMY_STANCE,
                    [
                        'return-type' => POP_RETURNTYPE_SLUGS,
                    ]
                );

            case 'stance':
                // The stance is the category
                return $typeResolver->resolveValue($resultItem, 'cat', $variables, $expressions, $options);

         // The Stance has no title, so return the excerpt instead.
         // Needed for when adding a comment on the Stance, where it will say: Add comment for...
            case 'title':
            case 'excerpt':
            case 'content':
                // Add the quotes around the content for the Stance
                $value = $postTypeAPI->getBasicPostContent($stance);
                if ($fieldName == 'title') {
                    return limitString($value, 100);
                } elseif ($fieldName == 'excerpt') {
                    return limitString($value, 300);
                }
                return $value;

            case 'stancetarget':
                return \PoP\PostMeta\Utils::getPostMeta($typeResolver->getID($stance), GD_METAKEY_POST_STANCETARGET, true);

            case 'has-stancetarget':
                // Cannot use !is_null because getPostMeta returns "" when there's no entry, instead of null
                return $typeResolver->resolveValue($resultItem, 'stancetarget', $variables, $expressions, $options);

            case 'stances':
                $query = array(
                    'limit' => -1,/*'posts-per-page' => -1,*/ // Bring all the results
                    'orderby' => NameResolverFacade::getInstance()->getName('popcms:dbcolumn:orderby:posts:date'),
                    'order' => 'ASC',
                );
                \UserStance_Module_Processor_CustomSectionBlocksUtils::addDataloadqueryargsStancesaboutpost($query, $typeResolver->getID($stance));

                return $postTypeAPI->getPosts($query, ['return-type' => POP_RETURNTYPE_IDS]);

            case 'has-stances':
                $referencedby = $typeResolver->resolveValue($resultItem, 'stances', $variables, $expressions, $options);
                return !empty($referencedby);

            case 'stance-pro-count':
            case 'stance-neutral-count':
            case 'stance-against-count':
                $cats = array(
                    'stance-pro-count' => POP_USERSTANCE_TERM_STANCE_PRO,
                    'stance-neutral-count' => POP_USERSTANCE_TERM_STANCE_NEUTRAL,
                    'stance-against-count' => POP_USERSTANCE_TERM_STANCE_AGAINST,
                );

                $query = array();
                \UserStance_Module_Processor_CustomSectionBlocksUtils::addDataloadqueryargsStancesaboutpost($query, $typeResolver->getID($stance));

                // Override the category
                $query['tax-query'][] = [
                    'taxonomy' => POP_USERSTANCE_TAXONOMY_STANCE,
                    'terms'    => $cats[$fieldName],
                ];

                // // All results
                // $query['limit'] = 0;

                return $postTypeAPI->getPostCount($query);
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'stancetarget':
                return UnionTypeHelpers::getUnionOrTargetTypeResolverClass(ContentEntityUnionTypeResolver::class);

            case 'stances':
                return StanceTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
