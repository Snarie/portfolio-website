<?php

$router->get("/", "controllers\HomeController@home");
$router->get("/about", "controllers\HomeController@about");

$router->get("/projects", "controllers\ProjectController@index");
$router->get("/projects/create", "controllers\ProjectController@create");
$router->post("/projects", "controller\ProjectController@store");
$router->get("/projects/{id}", "controller\ProjectController@show");
$router->get("/projects/{id}/edit", "controller\ProjectController@edit");
$router->put("/projects/{id}", "controller\ProjectController@update");
$router->delete("projects/{id}", "controller\ProjectController@destroy");

//$router->get("/profiles", "controllers\ProfileController@index");
//$router->get("/profiles/create", "controllers\ProfileController@create");
//$router->post("profiles", "controllers\ProfileController@store");
$router->get("/profiles/{id}", "controllers\ProfileController@show");
//$router->get("/profiles/{id}/edit", "controller\ProfileController@edit");
//$router->put("/profiles/{id}", "controller\ProfileController@update");
//$router->delete("/profiles/{id}", "controller\ProfileController@destroy");
