<?php

return [
    "/orm/entities/{entityId}" => [
        "get"    => "Phidias\Orm\Api\Entity\Controller::get({entityId})",
        "put"    => "Phidias\Orm\Api\Entity\Controller::save({input}, {entityId})",
        "delete" => "Phidias\Orm\Api\Entity\Controller::delete({entityId})"
    ]
];