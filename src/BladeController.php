<?php

namespace Fiskhandlarn;

use Sober\Controller\Controller;

/**
 * This is the blade controller class.
 *
 * @author Oskar Joelson <oskar@joelson.org>
 */
class BladeController extends Controller
{
    // Expose Sober\Controller\Controller funtionality to all BladeControllers
    protected $template = 'app';
}
