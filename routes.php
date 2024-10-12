<?php

$router->get("/", "HomeController@home");
$router->get("/about", "HomeController@about");

$router->get("/projects", "ProjectController@index");
$router->get("/projects/create", "ProjectController@create");
$router->post("/projects", "ProjectController@store");
$router->get("/projects/{id}", "ProjectController@show");
$router->get("/projects/{id}/edit", "ProjectController@edit");
$router->put("/projects/{id}", "ProjectController@update");
$router->delete("/projects/{id}", "ProjectController@destroy");

//$router->get("/profiles", "ProfileController@index");
//$router->get("/profiles/create", "ProfileController@create");
//$router->post("profiles", "ProfileController@store");
$router->get("/profiles/{id}", "ProfileController@show");
//$router->get("/profiles/{id}/edit", "ProfileController@edit");
//$router->put("/profiles/{id}", "ProfileController@update");
//$router->delete("/profiles/{id}", "ProfileController@destroy");
