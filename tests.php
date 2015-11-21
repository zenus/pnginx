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
//$greeter('hi'); // Hello World
$arr = array(
   'safdsfasfdl'
);

//die($arr[0][1]);

class hi {
   private $hi;

    public function set_hi($str){
        $this->hi = $str;
     }
    public function eccho (){
       echo $this->hi;
    }
}

function hi(){
   $hi = new hi();
    $hi->set_hi('wwww');
    return $hi;
}

$obj = hi();
$obj->eccho();

