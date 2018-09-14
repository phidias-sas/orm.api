<?php
namespace Phidias\Orm\Module\Entity;

use Phidias\Orm\Schema;

class Controller
{
    public static function collection($moduleId)
    {
        $entities = Entity::collection()
            ->allAttributes()
            ->match("module", $moduleId)
            ->find();

        $retval = [];
        foreach ($entities as $rawEntity) {
            $entity = json_decode($rawEntity->specification);
            $entity->id = $rawEntity->id;
            $retval[] = $entity;
        }

        return $retval;
    }

    public static function get($entityId)
    {
        $rawEntity = new Entity($entityId);

        return json_decode($rawEntity->specification);
    }

    public static function save($input, $moduleId, $entityId = null)
    {
        $retval = [];
        $entities = is_array($input) ? $input : [$input];

        foreach ($entities as $entityData) {
            $entity = new Entity($entityId);
            $entity->module = $moduleId;
            if (isset($entityData->name)) {
                $entity->name = $entityData->name;
            }
            $entity->specification = json_encode($entityData);
            $entity->save();

            $retval[] = $entityData;
        }

        return is_array($input) ? $retval : $retval[0];
    }

    public static function delete($entityId)
    {
        $entity = new Entity($entityId);
        $schema = Schema::getSchema($entity->name);

        $entity->delete();
        // $schema->drop();

        return $entity;
    }

    public static function install($moduleId)
    {
        $entities = Entity::collection()
            ->attribute("name")
            ->match("module", $moduleId)
            ->find();

        foreach ($entities as $entity) {
            $schema = Schema::getSchema($entity->name);
            $schema->patch();
            $schema->createTriggers();
        }
    }
}