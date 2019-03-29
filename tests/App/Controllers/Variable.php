<?php

namespace App\Controllers;

use Fiskhandlarn\BladeController;

class Variable extends BladeController
{
    public function machine(): string
    {
        return 'Voight-Kampff';
    }
}
