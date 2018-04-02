<?php
namespace Phidias\Orm\Api\Entity;

use Phidias\Orm\Api\Entity\Attribute\Entity as Attribute;
use Phidias\Orm\Api\Entity\Attribute\Controller as AttributeController;

use Phidias\Db\Table\Schema;

class Controller
{
    public static function collection()
    {
        return Entity::collection()
            ->allAttributes()
            ->attribute("attributes", Attribute::collection()
                ->allAttributes()
                ->addFilter(function ($attribute) {
                    $attribute->mysql = json_decode($attribute->mysql);
                })
            )
            ->addFilter(function ($entity) {
                $entity->mysql = json_decode($entity->mysql);
            });
    }

    public static function save($newEntityData, $entityId = null)
    {
        $entity = new Entity;
        $entity->setValues($newEntityData);
        $entity->id = $entityId;
        $entity->mysql = isset($newEntityData->mysql) && $newEntityData->mysql ? json_encode($newEntityData->mysql) : null;
        $entity->save();

        if (isset($newEntityData->attributes) && is_array($newEntityData->attributes)) {
            $entity->attributes = AttributeController::saveAll($entity->id, $newEntityData->attributes);
        }

        self::getSchema($entityId)->patch();

        return $entity;
    }

    public static function get($entityId)
    {
        return Entity::single()
            ->allAttributes()
            ->attribute("attributes", Attribute::collection()
                ->allAttributes()
                ->addFilter(function ($attr) {
                    $attr->mysql = json_decode($attr->mysql);
                })
            )
            ->addFilter(function ($entity) {
                $entity->mysql = json_decode($entity->mysql);
            })
            ->find($entityId)
            ->fetchAll();
    }

    public static function delete($entityId)
    {
        $entity = new Entity($entityId);
        $entity->delete();

        return $entity;
    }

    public static function factory($entityId)
    {
        return new \Phidias\Db\Orm\Collection( self::getSchema($entityId) );
    }

    public static function getSchema($entityId)
    {
        if (@class_exists($entityId)) {
            return $entityId::getSchema();
        }

        $entity = self::get($entityId);

        $schemaObject = new Schema();
        $schemaObject->table("orm_data_" . $entityId);
        $schemaObject->primaryKey(["id"]);

        foreach ($entity->attributes as $attribute) {

            if (isset($attribute->mysql->entity)) {
                $foreignSchema = self::getSchema($attribute->mysql->entity);

                $foreignAttribute = $foreignSchema->getAttribute($foreignSchema->getFirstKey());
                $foreignAttribute["column"] = $attribute->name;
                $schemaObject->attribute($attribute->name, $foreignAttribute);

                $acceptNull = isset($attribute->mysql->acceptNull) && $attribute->mysql->acceptNull;

                $schemaObject->foreignKey($attribute->name, [
                    "table"    => $foreignSchema->getTable(),
                    "column"   => $foreignSchema->getFirstKey(),
                    "onDelete" => $acceptNull ? "SET NULL" : "CASCADE",
                    "onUpdate" => $acceptNull ? "SET NULL" : "CASCADE"
                ]);

            } else {
                $schemaObject->attribute($attribute->name, self::sanitizeMySqlAttributeData($attribute->mysql));
            }

        }

        return $schemaObject;
    }

    private static function sanitizeMySqlAttributeData($mysql)
    {
        $retval = (array)$mysql;

        if (isset($retval["type"]) && $retval["type"] == "uuid") {
            $retval["type"]   = "varchar";
            $retval["length"] = 13;
            $retval["uuid"]   = true;
        }

        return $retval;
    }
}