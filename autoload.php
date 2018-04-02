<?php

spl_autoload_register(function ($class_name) {
    include '../dev/orm.api/src/' . $class_name . '.php';
});