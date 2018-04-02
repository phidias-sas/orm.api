<?php
namespace Phidias\Orm\Api\Endpoint;

class Entity extends \Phidias\Db\Orm\Entity
{
    public $id;
    public $path;
    public $controller;
    public $settings;

    protected static $schema = [
        "table" => "orm_api_endpoints",
        "keys"  => ["id"],

        "attributes" => [
            "id" => [
                "type" => "uuid"
            ],

            "path" => [
                "type"   => "varchar",
                "length" => 255
            ],

            "controller" => [
                "type"   => "varchar",
                "length" => 255
            ],

            "settings" => [
                "type"       => "mediumtext",
                "acceptNull" => true,
                "default"    => null
            ]
        ],

        // This field will be used with Phidias\Core\Url\Index.   Indexing this field will help a lot.
        "unique" => ["path"]
    ];
}