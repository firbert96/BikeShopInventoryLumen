<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/users','UserController@index');
$router->get('/inventory','InventoryController@index');
$router->get('/inventory_flows','InventoryFlowsController@index');

$router->post('/users','UserController@insert');
$router->put('/users','UserController@edit');
$router->put('/users/changePassword','UserController@changePassword');

$router->post('/inventory','InventoryController@insert');
$router->put('/inventory/editProduct','InventoryController@editProduct');
$router->put('/inventory/editQuantity','InventoryController@editQuantity');
$router->delete('/inventory','InventoryController@delete');
$router->get('/inventory/showInventory','InventoryController@showInventory');
$router->get('/inventory/showDetailProduct','InventoryController@showDetailProduct');

$router->post('/inventory_flows','InventoryFlowsController@insert');
$router->get('/inventory_flows/showBuyer','InventoryFlowsController@showBuyer');
$router->get('/inventory_flows/showSupplier','InventoryFlowsController@showSupplier');
$router->get('/inventory_flows/showDetailProduct','InventoryFlowsController@showDetailProduct');