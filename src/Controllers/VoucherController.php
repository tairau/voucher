<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Services\OutOfVouchersException;
use App\Services\VoucherIssuer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Message\Response;
use Sunrise\Http\Message\Response\HtmlResponse;

class VoucherController implements RequestHandlerInterface
{
    public function __construct(protected VoucherIssuer $issuer)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $voucher = $this->issuer->issue(session_id());
        } catch (OutOfVouchersException) {
            return new HtmlResponse(200, 'Out of vouchers');
        }

        $response = new Response(302);

        return $response->withHeader(
            'Location',
            'https://www.google.com/?query=' . $voucher['code']
        );
    }
}
