<?php

$origin = Config::is_in_production() ? Config::url() : "*";

header('Access-Control-Allow-Origin: '.$origin);
header('Content-Type: application/json; charset=utf-8');