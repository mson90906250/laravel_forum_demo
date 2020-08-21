<?php

namespace App\Inspections;

use Exception;

class InvalidWords
{
    protected $invalidWords = [
        'Dirty Word'
    ];

    public function detect($body)
    {
        foreach ($this->invalidWords as $invalidWord) {
            if (stripos($body, $invalidWord) !== false) {
                throw new Exception('The :attribute contain spam word');
            }
        }
    }
}
