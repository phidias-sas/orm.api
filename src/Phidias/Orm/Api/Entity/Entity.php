<?php
namespace Phidias\Orm\Api\Entity;

class Entity extends \Phidias\Db\Orm\Entity
{
    public $id;
    public $mysql;

    protected static $schema = [
        "table" => "orm_api_entities",
        "keys"  => ["id"],

        "attributes" => [
            "id" => [
                "type"   => "varchar",
                "length" => 64
            ],

            "mysql" => [
                "type"       => "mediumtext",
                "acceptNull" => true,
                "default"    => null
            ]
        ]
    ];
}