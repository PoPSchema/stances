<?php
namespace PoP\Stances\FieldResolvers;

use PoP\ComponentModel\Utils;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\FieldResolvers\AbstractFunctionalFieldResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\ModuleProcessors\ModuleProcessorManagerFacade;
use PoP\ComponentModel\GeneralUtils;
use PoP\Engine\Route\RouteUtils;
use PoP\Posts\TypeResolvers\PostTypeResolver;
use PoP\ComponentModel\Schema\TypeCastingHelpers;

class PostFunctionalFieldResolver extends AbstractFunctionalFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(
            PostTypeResolver::class,
        );
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'addstance-url',
            'loggedinuser-stances',
            'has-loggedinuser-stances',
            'editstance-url',
            'poststances-pro-url',
            'poststances-neutral-url',
            'poststances-against-url',
            'createstancebutton-lazy',
            'stances-lazy',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'addstance-url' => SchemaDefinition::TYPE_URL,
            'loggedinuser-stances' => TypeCastingHelpers::combineTypes(SchemaDefinition::TYPE_ARRAY, SchemaDefinition::TYPE_INT),
            'has-loggedinuser-stances' => SchemaDefinition::TYPE_BOOL,
            'editstance-url' => SchemaDefinition::TYPE_URL,
            'poststances-pro-url' => SchemaDefinition::TYPE_URL,
            'poststances-neutral-url' => SchemaDefinition::TYPE_URL,
            'poststances-against-url' => SchemaDefinition::TYPE_URL,
            'createstancebutton-lazy' => SchemaDefinition::TYPE_ARRAY,
            'stances-lazy' => SchemaDefinition::TYPE_ARRAY,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'addstance-url' => $translationAPI->__('', ''),
            'loggedinuser-stances' => $translationAPI->__('', ''),
            'has-loggedinuser-stances' => $translationAPI->__('', ''),
            'editstance-url' => $translationAPI->__('', ''),
            'poststances-pro-url' => $translationAPI->__('', ''),
            'poststances-neutral-url' => $translationAPI->__('', ''),
            'poststances-against-url' => $translationAPI->__('', ''),
            'createstancebutton-lazy' => $translationAPI->__('', ''),
            'stances-lazy' => $translationAPI->__('', ''),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $post = $resultItem;
        $cmspostsapi = \PoP\Posts\FunctionAPIFactory::getInstance();
        $cmseditpostsapi = \PoP\EditPosts\FunctionAPIFactory::getInstance();
        switch ($fieldName) {
            case 'addstance-url':
                $routes = array(
                    'addstance-url' => POP_USERSTANCE_ROUTE_ADDSTANCE,
                );
                $route = $routes[$fieldName];

                // $moduleprocessor_manager = ModuleProcessorManagerFacade::getInstance();
                // $input = [PoP_UserStance_Module_Processor_PostTriggerLayoutFormComponentValues::class, PoP_UserStance_Module_Processor_PostTriggerLayoutFormComponentValues::MODULE_FORMCOMPONENT_CARD_STANCETARGET];
                // $input_name = $moduleprocessor_manager->getProcessor($input)->getName($input);
                $input_name = POP_INPUTNAME_STANCETARGET;
                return GeneralUtils::addQueryArgs([
                    $input_name => $typeResolver->getId($post), 
                ], RouteUtils::getRouteURL($route));

            case 'loggedinuser-stances':
                $vars = \PoP\ComponentModel\Engine_Vars::getVars();
                if (!$vars['global-userstate']['is-user-logged-in']) {
                    return array();
                }
                $query = array(
                    'authors' => [$vars['global-userstate']['current-user-id']],
                );
                UserStance_Module_Processor_CustomSectionBlocksUtils::addDataloadqueryargsStancesaboutpost($query, $typeResolver->getId($post));

                return $cmspostsapi->getPosts($query, ['return-type' => POP_RETURNTYPE_IDS]);

            case 'has-loggedinuser-stances':
                $referencedby = $typeResolver->resolveValue($resultItem, 'loggedinuser-stances', $variables, $expressions, $options);
                return !empty($referencedby);

            case 'editstance-url':
                if ($referencedby = $typeResolver->resolveValue($resultItem, 'loggedinuser-stances', $variables, $expressions, $options)) {
                    return urldecode($cmseditpostsapi->getEditPostLink($referencedby[0]));
                }
                return null;

            case 'poststances-pro-url':
            case 'poststances-neutral-url':
            case 'poststances-against-url':
                $routes = array(
                    'poststances-pro-url' => POP_USERSTANCE_ROUTE_STANCES_PRO,
                    'poststances-neutral-url' => POP_USERSTANCE_ROUTE_STANCES_NEUTRAL,
                    'poststances-against-url' => POP_USERSTANCE_ROUTE_STANCES_AGAINST,
                );
                $url = $cmspostsapi->getPermalink($typeResolver->getId($post));
                return \PoP\ComponentModel\Utils::addRoute($url, $routes[$fieldName]);
          
            // Lazy Loading fields
            case 'createstancebutton-lazy':
                return null;

            case 'stances-lazy':
                return array();
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}