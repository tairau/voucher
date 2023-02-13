<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Services\VoucherIssuer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Message\Response\HtmlResponse;

class HomeController implements RequestHandlerInterface
{
    public function __construct(protected VoucherIssuer $issuer)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $view = file_get_contents(dirname(__DIR__).'/../views/index.html');

        return new HtmlResponse(200, $view);
    }
}
