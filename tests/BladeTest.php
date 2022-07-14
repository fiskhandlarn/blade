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

namespace Fiskhandlarn\Tests;

use App\Controllers\Method;
use App\Controllers\NonController;
use App\Controllers\Variable;
use App\Controllers\VariableDisabled;
use Fiskhandlarn\Blade;
use Fiskhandlarn\BladeControllerLoader;
use Fiskhandlarn\BladeFacade;
use Illuminate\Filesystem\Filesystem;
use WP_Mock;
use WP_Mock\Tools\TestCase;

/**
 * This is the blade test class.
 *
 * @author Oskar Joelson <oskar@joelson.org>
 */
class BladeTest extends TestCase
{
    private $blade;

    public function setUp(): void
    {
        parent::setUp();
        WP_Mock::setUp();

        $this->blade = new Blade('tests/views', 'tests/cache');

        WP_Mock::onFilter('blade/view/paths') /* @phpstan-ignore-line */
            ->with(BladeFacade::basePath('resources/views'))
            ->reply('tests/views');

        WP_Mock::onFilter('blade/cache/path') /* @phpstan-ignore-line */
            ->with(BladeFacade::basePath('storage/views'))
            ->reply('tests/cache');
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Blade::class, $this->blade);
    }

    public function testNullResponses()
    {
        $this->assertNull(
            $this->blade->thisMethodDoesNotExists()
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No such class found in namespace App\Controllers: NonExistingController');

        $this->assertNull(
            BladeControllerLoader::dataFromController('NonExistingController')
        );
    }

    public function testRender()
    {
        $this->cleanCacheDirectory();

        $this->assertEquals(
            'A new life awaits you in the Off-world colonies!',
            trim($this->blade->render('plain'))
        );

        $this->cleanCacheDirectory();

        $this->expectOutputString('A new life awaits you in the Off-world colonies!');
        blade('plain');

        $this->cleanCacheDirectory();

        $this->assertEquals(
            'A new life awaits you in the Off-world colonies!',
            trim(blade('plain', [], false))
        );
    }

    public function testCapillaryDilation()
    {
        $this->cleanCacheDirectory();

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim($this->blade->render('variable', ['machine' => 'Voight-Kampff']))
        );

        $this->cleanCacheDirectory();

        $this->expectOutputString('We call it Voight-Kampff for short.');
        blade('variable', ['machine' => 'Voight-Kampff']);

        $this->cleanCacheDirectory();

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim(blade('variable', ['machine' => 'Voight-Kampff'], false))
        );
    }

    public function testRenderController()
    {
        $this->cleanCacheDirectory();

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim($this->blade->renderController('variable', 'Variable'))
        );

        $this->cleanCacheDirectory();

        $this->expectOutputString('We call it Voight-Kampff for short.');
        blade_controller('variable', 'Variable');

        $this->cleanCacheDirectory();

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim(blade_controller('variable', 'Variable', [], false))
        );
    }

    public function testRenderControllerMethod()
    {
        $this->cleanCacheDirectory();

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim($this->blade->renderController('variable', 'Method'))
        );

        $this->cleanCacheDirectory();

        $this->expectOutputString('We call it Voight-Kampff for short.');
        blade_controller('variable', 'Method');

        $this->cleanCacheDirectory();

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim(blade_controller('variable', 'Method', [], false))
        );
    }

    public function testRenderControllerDisableOption()
    {
        $this->cleanCacheDirectory();

        $this->expectException(\ErrorException::class);

        $this->blade->renderController('variable', 'VariableDisabled');
    }

    public function testRenderControllerAdditionalData()
    {
        $this->cleanCacheDirectory();

        $this->assertEquals(
            'Deckard. B26354.',
            trim($this->blade->renderController('composer', 'Variable', ['badge' => 'B26354']))
        );

        $this->cleanCacheDirectory();

        $this->expectOutputString('Deckard. B26354.');
        blade_controller('composer', 'Variable', ['badge' => 'B26354']);

        $this->cleanCacheDirectory();

        $this->assertEquals(
            'Deckard. B26354.',
            trim(blade_controller('composer', 'Variable', ['badge' => 'B26354'], false))
        );
    }

    public function testRenderControllerClassException()
    {
        $this->cleanCacheDirectory();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No such class found in namespace App\Controllers: NonExistingClass');

        $this->blade->renderController('variable', 'NonExistingClass');
    }

    public function testRenderControllerInstanceException()
    {
        $this->cleanCacheDirectory();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Class does not extend BladeController: App\Controllers\NonController');

        $this->blade->renderController('variable', 'NonController');
    }

    public function testCreateCacheDirectory()
    {
        $this->assertDirectoryExists('tests/cache/1');
    }

    public function testPreserveGitIgnore()
    {
        $filesystem = new Filesystem();
        $filesystem->put('tests/cache/.gitignore', "");

        $this->cleanCacheDirectory();

        $this->assertFileExists('tests/cache/.gitignore');

        $filesystem->delete('tests/cache/.gitignore');
    }

    public function testDirective()
    {
        $this->cleanCacheDirectory();

        $this->blade->directive('datetime', function ($expression) {
            return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
        });

        $this->assertEquals(
            '2019-11-01 00:02:42',
            trim($this->blade->render('directive'))
        );

        $this->cleanCacheDirectory();

        blade_directive('datetime', function ($expression) {
            return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
        });

        $this->expectOutputString('2019-11-01 00:02:42');
        blade('directive');

        $this->cleanCacheDirectory();

        $this->assertEquals(
            '2019-11-01 00:02:42',
            trim(blade('directive', [], false))
        );
    }

    public function testComposer()
    {
        $this->cleanCacheDirectory();

        $this->blade->composer('composer', function ($view) {
            $view->with(['badge' => 'B26354']);
        });

        $this->assertEquals(
            'Deckard. B26354.',
            trim($this->blade->render('composer'))
        );

        $this->cleanCacheDirectory();

        blade_composer('composer', function ($view) {
            $view->with(['badge' => 'B26354']);
        });

        $this->expectOutputString('Deckard. B26354.');
        blade('composer');

        $this->cleanCacheDirectory();

        $this->assertEquals(
            'Deckard. B26354.',
            trim(blade('composer', [], false))
        );
    }

    public function testShare()
    {
        $this->cleanCacheDirectory();

        // shorthand
        $this->blade->share('position', '45');

        $this->assertEquals(
            'Track 45 right. Stop. Center and stop.',
            trim($this->blade->render('share-shorthand'))
        );

        // array
        $this->blade->share([
            'startPosition' => '224',
            'endPosition' => '176',
        ]);

        $this->assertEquals(
            'Enhance 224 to 176.',
            trim($this->blade->render('share-array'))
        );

        $this->cleanCacheDirectory();

        $this->expectOutputString('Track 45 right. Stop. Center and stop.' . 'Enhance 224 to 176.');

        blade_share('position', '45');

        blade('share-shorthand');

        blade_share([
            'startPosition' => '224',
            'endPosition' => '176',
        ]);

        blade('share-array');

        $this->cleanCacheDirectory();

        $this->assertEquals(
            'Track 45 right. Stop. Center and stop.',
            trim(blade('share-shorthand', [], false))
        );

        $this->assertEquals(
            'Enhance 224 to 176.',
            trim(blade('share-array', [], false))
        );
    }

    private function cleanCacheDirectory()
    {
        $this->blade->cleanCacheDirectory();
        BladeFacade::cleanCacheDirectory();
    }
}
