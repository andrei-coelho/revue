<?php 

use \Revue\Config as Config;

self::title(Config::get('name'));
self::description(Config::get('description'));