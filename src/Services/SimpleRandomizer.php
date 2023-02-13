<?php

declare(strict_types = 1);

namespace App\Services;

use App\Contracts\Randomizer;

use function bin2hex;
use function random_bytes;

class SimpleRandomizer implements Randomizer
{

    /**
     * @throws \Exception
     */
    public function make(int $length = 10): string
    {
        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }
}
