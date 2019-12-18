<?php
namespace PoP\Stances\Facades;

use PoP\Stances\TypeAPIs\StanceTypeAPIInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class StanceTypeAPIFacade
{
    public static function getInstance(): StanceTypeAPIInterface
    {
        return ContainerBuilderFactory::getInstance()->get('stance_type_api');
    }
}
