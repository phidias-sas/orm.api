<?php

return [
    "/orm/modules/{moduleId}/resources/{resourceId}" => [
        "get"    => "Phidias\Orm\Module\Resource\Controller::get({resourceId})",
        "put"    => "Phidias\Orm\Module\Resource\Controller::save({moduleId}, {input}, {resourceId})",
        "delete" => "Phidias\Orm\Module\Resource\Controller::delete({resourceId})"
    ]
];