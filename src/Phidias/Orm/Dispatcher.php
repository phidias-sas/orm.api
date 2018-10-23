<?php
namespace Phidias\Orm;

use Phidias\Core\Url\Index\Controller as UrlIndex;
use Phidias\Orm\Module\Resource\Entity as Resource;

use Phidias\Orm\Schema;


class Dispatcher
{
    public static function collection($settings, $query, $input, $request, $response)
    {
        $collection = self::toCollection($settings->collection);

        if (isset($settings->searchable) && isset($query->q)) {
            $collection->search($query->q, $settings->searchable);
        }

        if (!isset($settings->collection->limit)) {
            $collection->limit(50);
        }


        /*
        Filter from query string
        */
        if (isset($query->limit)) {
            $collection->limit($query->limit);
        }

        if (isset($query->page)) {
            $collection->page($query->page);
        }

        if (isset($query->sort)) {
            $collection->orderBy($query->sort, isset($query->desc) ? $query->desc : null);
        }


        /* Return a SINGLE element when fixed limit is set to 1 */
        if (isset($settings->collection->limit) && $settings->collection->limit == 1) {
            return $collection->find()->first();
        }

        return $collection;
    }

    public static function insert($settings, $query, $input, $request, $response)
    {
        $incomingRecords = is_array($input) ? $input : [$input];
        $collection = Schema::getCollection($settings->entity)->allAttributes();
        $retval = [];

        foreach ($incomingRecords as $incomingRecord) {
            $newEntity = new \stdClass;
            foreach ($settings->attributes as $attributeName => $attributeSource) {
                $newEntity->$attributeName = self::parseWildcards($attributeSource, ["record" => $incomingRecord]);
            }
            $collection->add($newEntity);
            $retval[] = $newEntity;
        }

        $collection->save();
        return $retval;
    }

    public static function resourceHandler($path)
    {
        $resourceObj = UrlIndex::match(Resource::collection(), "url", $path)
            ->allAttributes()
            ->limit(1)
            ->find()
            ->first();

        if (!$resourceObj) {
            return null;
        }

        $resource = json_decode($resourceObj->specification);
        $resource->id = $resourceObj->id;
        $resource->url = $resourceObj->url;

        $matchedUrlArguments = UrlIndex::getMatchedArguments($resource->url, $path);

        $resourceRaw = [];
        $methods = ["get", "post", "put", "delete"];
        foreach ($methods as $method) {
            if (!isset($resource->$method)) {
                continue;
            }

            $methodData = $resource->$method;
            $functionName = $methodData->dispatcher;

            $resourceRaw[$method] = [
                "authorization" => function($authentication) {
                    if (!$authentication) {
                        return false;
                    }
                },

                "controller" => function($query, $input, $request, $response) use ($functionName, $methodData, $matchedUrlArguments) {

                    $settings = Dispatcher::parseWildcards($methodData->settings, [
                        "now"   => time(),
                        "url"   => $matchedUrlArguments,
                        "query" => $query,
                        "input" => $input
                    ], true);

                    return Dispatcher::$functionName($settings, $query, $input, $request, $response);
                }
            ];
        }

        return \Phidias\Api\Resource::factory($resourceRaw);
    }

    public static function toCollection($jsonQuery, $isNested = false)
    {
        $collection = Schema::getCollection($jsonQuery->entity);

        foreach ($jsonQuery->select as $attributeName => $attributeSource) {
            if ($attributeSource === true || $attributeSource === "1") {
                $collection->attribute($attributeName);
            } else if (isset($attributeSource->entity)) {
                $collection->attribute($attributeName, self::toCollection($attributeSource, true));
            } else {
                $collection->attribute($attributeName, $attributeSource);
            }
        }

        if (isset($jsonQuery->match)) {
            $collection->match($jsonQuery->match);
        }

        if (isset($jsonQuery->where)) {
            $collection->where($jsonQuery->where);
        }

        if (!$isNested) {
            if (isset($jsonQuery->limit)) {
                $collection->limit($jsonQuery->limit);
            }
        }


        return $collection;
    }

    public static function parseWildcards($string, $data, $preserveUnmatched = false)
    {
        if (is_array($string)) {
            $retval = [];
            foreach ($string as $k => $value) {
                $retval[$k] = self::parseWildcards($value, $data, $preserveUnmatched);
            }
            return $retval;
        }

        if (is_object($string)) {
            $retval = new \stdClass;
            foreach ($string as $k => $value) {
                $retval->$k = self::parseWildcards($value, $data, $preserveUnmatched);
            }
            return $retval;
        }


        $matches = [];
        preg_match_all('/\${(.+?)}/', $string, $matches);

        $targets = $matches[0];
        $replacements = [];

        foreach ($targets as $k => $target) {
            try {
                $targetValue = self::getProperty($matches[1][$k], $data);
                $replacements[] = $targetValue;
            } catch (\Exception $e) {
                // trigger_error("Value '$target' not defined", E_USER_WARNING);
                $replacements[] = $preserveUnmatched ? $target : null;
            }
        }

        return str_replace($targets, $replacements, $string);
    }

    /*
    $data = new \stdClass;
    $data->transaction = [
        "person" => [
            "firstName" => "Sigismundo"
        ]
    ];

    $firstName = self::getProperty("transaction.person.firstName", $data)
    */
    private static function getProperty($propertyPath, $var)
    {
        if (!$propertyPath) {
            return $var;
        }

        if (!is_array($var) && !is_object($var)) {
            throw new \Exception("undefined property '$propertyPath'");
        }

        $parts = explode(".", $propertyPath);
        $currentKey = array_shift($parts);
        $remainingPath = implode(".", $parts);

        if (is_array($var)) {
            if (!isset($var[$currentKey])) {
                throw new \Exception("undefined property '$propertyPath'");
            }
            return self::getProperty($remainingPath, $var[$currentKey]);
        } else {
            if (!property_exists($var, $currentKey)) {
                throw new \Exception("undefined property '$propertyPath'");
            }
            return self::getProperty($remainingPath, $var->$currentKey);
        }
    }
}