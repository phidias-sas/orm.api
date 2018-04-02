<?php

return [
    "/orm/endpoints" => [
        "get" => [
            "controller" => "Phidias\Orm\Api\Endpoint\Controller::collection()"
        ],

        "post" => [
            "controller" => "Phidias\Orm\Api\Endpoint\Controller::save({input})",
            "filter" => function($output, $response) {
                $response
                    ->status(201)
                    ->header("Location", "data/endpoints/{$output->id}");
            }
        ]
    ]
];