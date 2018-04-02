<?php

return [
    "/orm/endpoints/{endpointId}" => [
        "get"    => "Phidias\Orm\Api\Endpoint\Controller::get({endpointId})",
        "put"    => "Phidias\Orm\Api\Endpoint\Controller::save({input}, {endpointId})",
        "delete" => "Phidias\Orm\Api\Endpoint\Controller::delete({endpointId})"
    ]
];