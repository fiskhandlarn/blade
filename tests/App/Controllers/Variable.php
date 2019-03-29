<?php

namespace App\Controllers;

use Fiskhandlarn\BladeController;

class Variable extends BladeController
{
    private $machine;

    public function __before()
    {
        // runs after this->data is set up, but before the class methods are run
        $this->machine = 'Voight-Kampff';
    }

    public function machine(): string
    {
        // so we can test __before():
        return $this->machine;
    }
}
