<?php
namespace Phidias\Orm\Module;

class Entity extends \Phidias\Db\Orm\Entity
{
    public $id;
    public $name;

    protected static $schema = [
        "table" => "orm_modules",
        "keys"  => ["id"],

        "attributes" => [
            "id" => [
                "type" => "uuid"
            ],

            "name" => [
                "type"       => "varchar",
                "length"     => 128,
                "acceptNull" => true,
                "default"    => null
            ]
        ]
    ];
}