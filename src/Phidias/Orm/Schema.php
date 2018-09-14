<?php
namespace Phidias\Orm;

use Phidias\Orm\Module\Entity\Entity as Entity;
use Phidias\Db\Table\Schema as DbSchema;

class Schema
{
    public static function getCollection($entityName)
    {
        return new \Phidias\Db\Orm\Collection(self::getSchema($entityName));
    }

    public static function getSchema($entityName)
    {
        if (!is_string($entityName)) {
            return self::getSchemaFromObject($entityName);
        }

        if (@class_exists($entityName)) {
            return $entityName::getSchema();
        }

        $entity = Entity::single()
            ->match("name", $entityName)
            ->allAttributes()
            ->find()
            ->first();

        if (!$entity) {
            throw new \Exception("Entity '$entityName' not found");
        }

        $schema = json_decode($entity->specification);
        return self::getSchemaFromObject($schema);
    }

    private static function getSchemaFromObject($schema)
    {

        $dbSchema = new DbSchema();
        $dbSchema->table(self::getTableName($schema->name));
        $dbSchema->primaryKey($schema->keys);

        foreach ((array)$schema->attributes as $attributeName => $attribute) {

            if (isset($attribute->entity)) {
                $foreignSchema = self::getSchema($attribute->entity);

                $foreignAttribute = $foreignSchema->getAttribute($foreignSchema->getFirstKey());
                $foreignAttribute["column"] = $attributeName;
                $dbSchema->attribute($attributeName, $foreignAttribute);

                $acceptNull = isset($attribute->acceptNull) && $attribute->acceptNull;

                $dbSchema->foreignKey($attributeName, [
                    "table"    => $foreignSchema->getTable(),
                    "column"   => $foreignSchema->getFirstKey(),
                    "onDelete" => $acceptNull ? "SET NULL" : "CASCADE",
                    "onUpdate" => $acceptNull ? "SET NULL" : "CASCADE"
                ]);

            } else {
                $dbSchema->attribute($attributeName, self::sanitizeAttributeData($attribute));
            }
        }

        // !!! partial trigger support.  Missing before/after and update
        if (isset($schema->triggers->insert)) {
            $dbSchema->trigger("after", "insert", self::parseSql($schema->triggers->insert));
        }

        return $dbSchema;
    }



    private static function getTableName($entityName)
    {
        return "_orm_" . strtolower(str_replace("\\", "_", $entityName));
    }


    private static function parseSql($sql)
    {
        $matches = [];
        preg_match_all('/{(.+?)}/', $sql, $matches);

        $targets = $matches[0];
        $replacements = [];

        foreach ($matches[1] as $k => $entityName) {
            $replacements[] = '`'.self::getTableName($entityName).'`';
        }

        return str_replace($targets, $replacements, $sql);
    }


    private static function sanitizeAttributeData($attributeData)
    {
        $retval = (array)$attributeData;

        if (isset($retval["type"]) && $retval["type"] == "uuid") {
            $retval["type"]   = "varchar";
            $retval["length"] = 13;
            $retval["uuid"]   = true;
        }

        if (isset($retval["type"]) && $retval["type"] == "date") {
            $retval["type"]     = "integer";
            $retval["unsigned"] = true;
        }

        return $retval;
    }

}