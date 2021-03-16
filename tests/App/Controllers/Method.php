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

/**
 * @author Oskar Joelson <oskar@joelson.org>
 */

declare(strict_types=1);

namespace App\Controllers;

use Fiskhandlarn\BladeController;

class Method extends BladeController
{
    public function machine()
    {
        return 'Voight-Kampff';
    }
}
