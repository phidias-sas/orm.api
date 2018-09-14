<?php
return [
    "/orm/modules" => [
        "get"  => "\Phidias\Orm\Module\Controller::collection()",
        "post" => "\Phidias\Orm\Module\Controller::save({input})"
    ]
];