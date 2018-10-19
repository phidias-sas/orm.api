<?php
namespace Phidias\Orm\Sql;

class Controller
{
    public static function patch($className)
    {
        if (!trim($className)) {
            return;
        }

        if (!@class_exists($className)) {
            return "Class does not exist";
        }

        if (!is_subclass_of($className, "\Phidias\Db\Orm\Entity")) {
            return "Class is not a \Phidias\Db\Orm\Entity";
        }

        try {
            $schema = $className::getSchema();
            $schema->patch();
            return "Done :)";
        } catch (\Exception $e) {
            return ["Error creating table", $e];
        }

    }
}