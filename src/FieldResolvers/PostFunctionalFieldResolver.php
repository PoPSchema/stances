<?php

declare(strict_types=1);

namespace PoP\Stances\FieldResolvers;

use PoP\Engine\Route\RouteUtils;
use PoP\ComponentModel\Misc\GeneralUtils;
use PoP\Posts\Facades\PostTypeAPIFacade;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\Content\FieldInterfaces\CustomPostFieldInterfaceResolver;
use PoP\ComponentModel\FieldResolvers\AbstractFunctionalFieldResolver;
use PoP\ComponentModel\State\ApplicationState;
use PoP\ComponentModel\Misc\RequestUtils;

class PostFunctionalFieldResolver extends AbstractFunctionalFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(
            CustomPostFieldInterfaceResolver::class,
        );
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'addStanceURL',
            'loggedInUserStances',
            'hasLoggedInUserStances',
            'editStanceURL',
            'postStancesProURL',
            'postStancesNeutralURL',
            'postStancesAgainstURL',
            'createStanceButtonLazy',
            'stancesLazy',
            'stanceName',
            'catName',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'addStanceURL' => SchemaDefinition::TYPE_URL,
            'loggedInUserStances' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_INT),
            'hasLoggedInUserStances' => SchemaDefinition::TYPE_BOOL,
            'editStanceURL' => SchemaDefinition::TYPE_URL,
            'postStancesProURL' => SchemaDefinition::TYPE_URL,
            'postStancesNeutralURL' => SchemaDefinition::TYPE_URL,
            'postStancesAgainstURL' => SchemaDefinition::TYPE_URL,
            'createStanceButtonLazy' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'stancesLazy' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'stanceName' => SchemaDefinition::TYPE_STRING,
            'catName' => SchemaDefinition::TYPE_STRING,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'addStanceURL' => $translationAPI->__('', ''),
            'loggedInUserStances' => $translationAPI->__('', ''),
            'hasLoggedInUserStances' => $translationAPI->__('', ''),
            'editStanceURL' => $translationAPI->__('', ''),
            'postStancesProURL' => $translationAPI->__('', ''),
            'postStancesNeutralURL' => $translationAPI->__('', ''),
            'postStancesAgainstURL' => $translationAPI->__('', ''),
            'createStanceButtonLazy' => $translationAPI->__('', ''),
            'stancesLazy' => $translationAPI->__('', ''),
            'stanceName' => $translationAPI->__('', ''),
            'catName' => $translationAPI->__('', ''),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $post = $resultItem;
        $postTypeAPI = PostTypeAPIFacade::getInstance();
        $cmseditpostsapi = \PoP\EditPosts\FunctionAPIFactory::getInstance();
        switch ($fieldName) {
            case 'addStanceURL':
                $routes = array(
                    'addStanceURL' => POP_USERSTANCE_ROUTE_ADDSTANCE,
                );
                $route = $routes[$fieldName];

                // $moduleprocessor_manager = ModuleProcessorManagerFacade::getInstance();
                // $input = [PoP_UserStance_Module_Processor_PostTriggerLayoutFormComponentValues::class, PoP_UserStance_Module_Processor_PostTriggerLayoutFormComponentValues::MODULE_FORMCOMPONENT_CARD_STANCETARGET];
                // $input_name = $moduleprocessor_manager->getProcessor($input)->getName($input);
                $input_name = POP_INPUTNAME_STANCETARGET;
                return GeneralUtils::addQueryArgs([
                    $input_name => $typeResolver->getID($post),
                ], RouteUtils::getRouteURL($route));

            case 'loggedInUserStances':
                $vars = ApplicationState::getVars();
                if (!$vars['global-userstate']['is-user-logged-in']) {
                    return array();
                }
                $query = array(
                    'authors' => [$vars['global-userstate']['current-user-id']],
                );
                \UserStance_Module_Processor_CustomSectionBlocksUtils::addDataloadqueryargsStancesaboutpost($query, $typeResolver->getID($post));

                return $postTypeAPI->getPosts($query, ['return-type' => POP_RETURNTYPE_IDS]);

            case 'hasLoggedInUserStances':
                $referencedby = $typeResolver->resolveValue($resultItem, 'loggedInUserStances', $variables, $expressions, $options);
                return !empty($referencedby);

            case 'editStanceURL':
                if ($referencedby = $typeResolver->resolveValue($resultItem, 'loggedInUserStances', $variables, $expressions, $options)) {
                    return urldecode($cmseditpostsapi->getEditPostLink($referencedby[0]));
                }
                return null;

            case 'postStancesProURL':
            case 'postStancesNeutralURL':
            case 'postStancesAgainstURL':
                $routes = array(
                    'postStancesProURL' => POP_USERSTANCE_ROUTE_STANCES_PRO,
                    'postStancesNeutralURL' => POP_USERSTANCE_ROUTE_STANCES_NEUTRAL,
                    'postStancesAgainstURL' => POP_USERSTANCE_ROUTE_STANCES_AGAINST,
                );
                $url = $postTypeAPI->getPermalink($post);
                return RequestUtils::addRoute($url, $routes[$fieldName]);

            // Lazy Loading fields
            case 'createStanceButtonLazy':
                return null;

            case 'stancesLazy':
                return array();

            case 'stanceName':
            case 'catName':
                $selected = $typeResolver->resolveValue($resultItem, 'stance', $variables, $expressions, $options);
                $params = array(
                    'selected' => $selected
                );
                $stance = new \GD_FormInput_Stance($params);
                return $stance->getSelectedValue();
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
