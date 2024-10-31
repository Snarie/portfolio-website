<?php

use App\Requests\FilterProjectRequest;
use App\Requests\StoreProjectRequest;
use App\Requests\StoreToolRequest;
use App\Requests\UpdateProjectRequest;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Requests\UpdateToolRequest;

$router->get("/", "HomeController@home");
$router->get("/about", "HomeController@about");

$router->get("/projects", "ProjectController@index");
$router->get("/projects/create", "ProjectController@create");
$router->post("/projects", "ProjectController@store", StoreProjectRequest::class);
$router->get("/projects/{project}", "ProjectController@show");
$router->get("/projects/{project}/edit", "ProjectController@edit");
$router->put("/projects/{project}", "ProjectController@update", UpdateProjectRequest::class);
$router->delete("/projects/{project}", "ProjectController@destroy");

$router->post("/projects/filter", "ProjectController@filter", FilterProjectRequest::class);

$router->get('/tools', "ToolController@index");
$router->post("/tools", "ToolController@store", StoreToolRequest::class);
$router->put("/tools/{tool}", "ToolController@update", UpdateToolRequest::class);
$router->delete("/tools/{tool}/delete", "ToolController@delete");

$router->get('/login', "UserController@login", 'auth.login');
$router->post('/login', "UserController@storeLogin", LoginRequest::class);
$router->get('/register', "UserController@register", 'auth.register');
$router->post('/register', "UserController@storeRegister", RegisterRequest::class);
$router->delete('/logout', "UserController@logout");
