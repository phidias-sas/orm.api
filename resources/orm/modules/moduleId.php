<?php
return [
    "/orm/modules/{moduleId}" => [
        "get"    => "\Phidias\Orm\Module\Controller::get({moduleId})",
        "put"    => "\Phidias\Orm\Module\Controller::save({input}, {moduleId})",
        "delete" => "\Phidias\Orm\Module\Controller::delete({moduleId})"
    ]
];