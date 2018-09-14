<?php
return [
    "/orm/modules/{moduleId}/entities" => [
        "get" => "Phidias\Orm\Module\Entity\Controller::collection({moduleId})",
        "post" => "Phidias\Orm\Module\Entity\Controller::save({input}, {moduleId})"
    ]
];