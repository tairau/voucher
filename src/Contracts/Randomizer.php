<?php

declare(strict_types = 1);

namespace App\Contracts;

interface Randomizer
{
    public function make(int $length = 10): string;
}
