<?php

use App\Controller\TestController;
use App\Core\Route;

Route::get('api', [TestController::class, 'index']);
