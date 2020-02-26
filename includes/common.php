<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

function fp_function($callback, $args){
    return call_user_func_array($callback, $args);
}