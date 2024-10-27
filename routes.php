<?php

use App\Requests\StoreProjectRequest;
use App\Requests\UpdateProjectRequest;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;

$router->get("/", "HomeController@home");
$router->get("/about", "HomeController@about");

$router->get("/projects", "ProjectController@index");
$router->get("/projects/create", "ProjectController@create");
$router->post("/projects", "ProjectController@store", StoreProjectRequest::class);
$router->get("/projects/{project}", "ProjectController@show");
$router->get("/projects/{project}/edit", "ProjectController@edit");
$router->put("/projects/{project}", "ProjectController@update", UpdateProjectRequest::class);
$router->delete("/projects/{project}", "ProjectController@destroy");

$router->get('/login', "UserController@login");
$router->post('/login', "UserController@storeLogin", LoginRequest::class);
$router->get('/register', "UserController@register");
$router->post('/register', "UserController@storeRegister", RegisterRequest::class);
