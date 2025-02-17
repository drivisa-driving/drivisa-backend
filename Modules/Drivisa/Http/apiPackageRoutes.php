<?php

use Illuminate\Routing\Router;

use \Modules\Drivisa\Http\Controllers\Api\admin\{
    PackageTypeController,
    PackageController,
};

/** @var Router $router */

// all api prefixed with  'v1/drivisa/admin'

// package type endpoints
$router->get("/all-package-types", [PackageTypeController::class, 'allPackages']);
$router->get("/package-types", [PackageTypeController::class, 'index']);
$router->post("/package-types", [PackageTypeController::class, 'store']);
$router->get("/package-types/{id}", [PackageTypeController::class, 'single']);
$router->put("/package-types/{id}", [PackageTypeController::class, 'update']);
$router->delete("/package-types/{id}", [PackageTypeController::class, 'delete']);
$router->get("/with-packages-count", [PackageTypeController::class, 'withPackagesCount']);


// packages endpoints
$router->get("/packages", [PackageController::class, 'index']);
$router->get("/allPackages", [PackageController::class, 'allPackages']);
$router->get("/packages/only-bde", [PackageController::class, 'onlyBde']);
$router->get("/packages/get-selected-packages", [PackageController::class, 'getSelectedPackages']);
$router->get("/packages/all", [PackageController::class, 'all']);
$router->post("/packages", [PackageController::class, 'store']);
$router->get("/packages/{id}", [PackageController::class, 'single']);
$router->put("/packages/{id}", [PackageController::class, 'update']);
$router->delete("/packages/{id}", [PackageController::class, 'delete']);
