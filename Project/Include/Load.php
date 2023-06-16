<?php

spl_autoload_register(static function($class){
    require_once 'Classes/'. $class .'.php';
});
