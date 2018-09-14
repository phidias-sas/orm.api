<?php
namespace Phidias\Orm\Module\Entity;

class Entity extends \Phidias\Db\Orm\Entity
{
    public $id;
    public $module;
    public $name;
    public $specification;

    protected static $schema = [
        "table" => "orm_module_entities",
        "keys"  => ["id"],

        "attributes" => [
            "id" => [
                "type" => "uuid"
            ],

            "module" => [
                "entity"   => "Phidias\Orm\Module\Entity",
                "onDelete" => "CASCADE",
                "onUpdate" => "CASCADE"
            ],

            "name" => [
                "type"       => "varchar",
                "length"     => 128,
                "acceptNull" => true,
                "default"    => null
            ],

            "specification" => [
                "type"       => "text",
                "acceptNull" => true,
                "default"    => null
            ]
        ],

        "unique" => ["name"]
    ];
}