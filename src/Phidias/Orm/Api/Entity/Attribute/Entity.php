<?php
namespace Phidias\Orm\Api\Entity\Attribute;

class Entity extends \Phidias\Db\Orm\Entity
{
    public $id;
    public $entity;
    public $name;
    public $mysql;

    protected static $schema = [
        "table" => "orm_api_entities_attributes",
        "keys"  => ["id"],

        "attributes" => [
            "id" => [
                "type" => "uuid"
            ],

            "entity" => [
                "entity"    => "Phidias\Orm\Api\Entity\Entity",
                "attribute" => "id",
                "onDelete"  => "CASCADE",
                "onUpdate"  => "CASCADE"
            ],

            "name" => [
                "type"       => "varchar",
                "length"     => 128,
                "acceptNull" => true,
                "default"    => null
            ],

            "mysql" => [
                "type"       => "mediumtext",
                "acceptNull" => true,
                "default"    => null
            ]
        ]
    ];
}