<?php
/**
 * Created by PhpStorm.
 * User: zenus
 * Date: 15-11-17
 * Time: 下午11:26
 */
function createGreeter() {
    return function($why) {
        echo "Hello ".$why;
    };
}

$greeter = createGreeter("World");
$greeter('hi'); // Hello World

