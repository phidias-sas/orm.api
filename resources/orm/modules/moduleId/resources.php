<?php

return [
    "/orm/modules/{moduleId}/resources" => [
        "get" => "Phidias\Orm\Module\Resource\Controller::collection({moduleId})",
        "post" => "Phidias\Orm\Module\Resource\Controller::save({moduleId}, {input})"
    ]
];