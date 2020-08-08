<?php

declare(strict_types=1);

namespace PoPSchema\Stances\Facades;

use PoPSchema\Stances\TypeAPIs\StanceTypeAPIInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class StanceTypeAPIFacade
{
    public static function getInstance(): StanceTypeAPIInterface
    {
        return ContainerBuilderFactory::getInstance()->get('stance_type_api');
    }
}
