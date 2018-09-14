<?php
namespace Phidias\Orm\Module\Resource;

use Phidias\Orm\Module\Resource\Entity as Resource;
use Phidias\Core\Url\Index\Controller as UrlIndex;

class Controller
{
    public static function collection($moduleId)
    {
        $retval = [];

        $resources = Resource::collection()
            ->match("module", $moduleId)
            ->allAttributes()
            ->find();

        foreach ($resources as $resource) {
            $resourceObj = new \stdClass;
            $resourceObj->id = $resource->id;
            $resourceObj->url = $resource->url;

            $specification = json_decode($resource->specification);
            foreach ($specification as $propName => $propValue) {
                $resourceObj->$propName = $propValue;
            }

            $retval[] = $resourceObj;
        }


        return $retval;
    }

    public static function save($moduleId, $input, $resourceId = null)
    {
        $incomingResources = is_array($input) ? $input : [$input];
        $retval = [];

        foreach ($incomingResources as $incomingResource) {
            $resource = new Resource;
            $resource->id = $resourceId ?: (isset($incomingResource->id) ? $incomingResource->id: null);
            $resource->module = $moduleId;
            $resource->url = UrlIndex::store($incomingResource->url);

            $specification = new \stdClass;
            foreach ($incomingResource as $propName => $propValue) {
                if ($propName == "id" || $propName == "module" || $propName == "url") {
                    continue;
                }
                $specification->$propName = $propValue;
            }
            $resource->specification = json_encode($specification);

            $resource->save();

            $incomingResource->id = $resource->id;
            $retval[] = $incomingResource;
        }

        return is_array($input) ? $retval : $retval[0];
    }

    public static function get($resourceId)
    {
        $resource = new Resource($resourceId);

        $retval = json_decode($resource->specification);
        $retval->url = $resource->url;

        return $retval;
    }

    public static function delete($resourceId)
    {
        $resource = new Resource($resourceId);
        return $resource->delete();
    }

}