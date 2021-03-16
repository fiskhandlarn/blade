<?php

/**
 * This file is part of blade.
 *
 * blade is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * blade is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with blade.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

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
