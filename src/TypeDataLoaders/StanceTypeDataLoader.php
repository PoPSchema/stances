<?php
namespace PoP\Stances\TypeDataLoaders;

use PoP\Posts\TypeDataLoaders\PostTypeDataLoader;

class StanceTypeDataLoader extends PostTypeDataLoader
{
    public function getDataFromIdsQuery(array $ids): array
    {
        $query = parent::getDataFromIdsQuery($ids);
        $query['post-types'] = array(POP_USERSTANCE_POSTTYPE_USERSTANCE);
        return $query;
    }

    /**
     * Function to override
     */
    public function getQuery($query_args): array
    {
        $query = parent::getQuery($query_args);

        $query['post-types'] = array(POP_USERSTANCE_POSTTYPE_USERSTANCE);

        return $query;
    }
}
