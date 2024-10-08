<?php

$router->get("/", "controllers\HomeController@home");
$router->get("/about", "controllers\HomeController@about");

//$router->get("/profiles", "controllers\ProfileController@index");
//$router->get("/profiles/create", "controllers\ProfileController@create");
//$router->post("profiles", "controllers\ProfileController@store");
$router->get("/profiles/{id}", "controllers\ProfileController@show");
//$router->get("/profiles/{id}/edit", "controller\ProfileController@edit");
//$router->put("/profiles/{id}", "controller\ProfileController@update");
//$router->delete("/profiles/{id}", "controller\ProfileController@destroy");
