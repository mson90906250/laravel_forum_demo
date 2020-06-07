<?php

namespace App\Inspections;

use Exception;

class KeyHeldDown
{
    public function detect($body)
    {
        if (preg_match('/(.)\\1{3,}/', $body, $matches)) {
            throw new Exception('The body contain key held down word');
        }
    }
}
