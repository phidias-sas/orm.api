<?php

return [
    "/orm/modules/{moduleId}/entities/{entityId}" => [
        "get"    => "Phidias\Orm\Module\Entity\Controller::get({entityId})",
        "put"    => "Phidias\Orm\Module\Entity\Controller::save({input}, {moduleId}, {entityId})",
        "delete" => "Phidias\Orm\Module\Entity\Controller::delete({entityId})"
    ]
];