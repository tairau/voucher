<?php

use App\Controllers\HomeController;
use App\Controllers\VoucherController;

/** @var Sunrise\Http\Router\RouteCollector $this */


$container = $this->getContainer();

$this->get('home', '/', $container->get(HomeController::class));
$this->post('take.voucher', '/voucher', $container->get(VoucherController::class));
