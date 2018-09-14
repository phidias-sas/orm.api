<?php
namespace Phidias\Orm\Module\Resource;

class Entity extends \Phidias\Db\Orm\Entity
{
    public $id;
    public $module;
    public $url;
    public $specification;

    protected static $schema = [
        "table" => "orm_module_resources",
        "keys"  => ["id"],

        "attributes" => [
            "id" => [
                "type" => "uuid"
            ],

            "module" => [
                "entity" => "Phidias\Orm\Module\Entity"
            ],

            "url" => [
                "type"   => "varchar",
                "length" => 255
            ],

            "specification" => [
                "type" => "text"
            ]
        ],

        // This field will be used with Phidias\Core\Url\Index.   Indexing this field will help a lot.
        "unique" => ["url"]
    ];
}