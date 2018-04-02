<?php
namespace Phidias\Orm\Api\Endpoint;

use Phidias\Orm\Api\Endpoint\Entity as Endpoint;
use Phidias\Core\Url\Index\Controller as UrlIndex;

class Controller
{
    public static function collection()
    {
        return Endpoint::collection()
            ->allAttributes()
            ->addFilter(function ($endpoint) {
                $endpoint->settings = json_decode($endpoint->settings);
            });
    }

    public static function get($endpointId)
    {
        $endpoint = new Endpoint($endpointId);
        $endpoint->settings = json_decode($endpoint->settings);

        return $endpoint;
    }

    public static function save($newEndpointData, $endpointId = null)
    {
        $endpoint = new Endpoint($endpointId);
        $endpoint->setValues($newEndpointData);
        $endpoint->path     = UrlIndex::store($endpoint->path);
        $endpoint->settings = json_encode($endpoint->settings);

        $endpoint->save();

        return $endpoint;
    }

    public static function delete($endpointId)
    {
        $endpoint = new Endpoint($endpointId);
        $endpoint->delete();

        return $endpoint;
    }


    public static function getResource($path)
    {
        $endpoint = UrlIndex::match(Endpoint::collection(), "path", $path)
            ->allAttributes()
            ->addFilter(function($endpoint) use ($path) {
                $matchedArguments   = UrlIndex::getMatchedArguments($endpoint->path, $path);
                $parsedSettings     = str_replace(array_keys($matchedArguments), array_values($matchedArguments), $endpoint->settings);
                $endpoint->settings = json_decode($parsedSettings);
            })
            ->limit(1)
            ->find()
            ->first();

        if (!$endpoint) {
            return null;
        }

        $controller   = new \Phidias\Orm\Api\Controller($endpoint->settings);
        $functionName = strtolower($endpoint->controller);

        return \Phidias\Api\Resource::factory([
            "get" => function($query, $input, $request, $response) use ($controller, $functionName) {
                return $controller->$functionName($query, $input, $request, $response);
            }
        ]);
    }
}
