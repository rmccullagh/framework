<?php

error_reporting(-1);

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/Como/Solace/ClassLoader.php';

/*
 * Register the core libraries needed for the application
 * This sets the ./src directory as the autoloaded dir
 */
Como\Solace\ClassLoader::register();

use Como\App\app;
use Como\Log\Log;

Log::debug("Adding :id param");

app::param(':id', "/^[0-9]+$/");

// middleware example
app::using(function($req, $res) {
   $res->set("X-Your-IP", $req->ip);
});

app::get('/', function($req, $res) {
  var_dump($req, $res);
  
  //$res->send("Default");
});

app::get('/a', function($req, $res) {
  $res->send("a");
});

app::get("/user/:id/:id", function($req, $res) {  
  $res->send($req->route);
});

app::get('/product/:id', function($req, $res) {
  $res->send("Hello, World");
});

app::run();


