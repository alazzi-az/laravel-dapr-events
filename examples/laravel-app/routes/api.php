<?php

use AlazziAz\DaprEvents\Support\RouteMacros;
use Illuminate\Support\Facades\Route;

RouteMacros::register();

Route::daprSubscriptions();
