<?php

/*
 * This file is part of fiskhandlarn/blade.
 *
 * (c) Oskar Joelson <oskar@joelson.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fiskhandlarn\Tests;

use Fiskhandlarn\Blade;
use Fiskhandlarn\BladeFacade;
use WP_Mock\Tools\TestCase;

require __DIR__ . '/App/Controllers/Variable.php';

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
        \WP_Mock::setUp();

        $this->blade = new Blade('tests/views', 'tests/cache');

        \WP_Mock::onFilter('blade/view/paths')
            ->with(BladeFacade::base_path('resources/views'))
            ->reply('tests/views');

        \WP_Mock::onFilter('blade/cache/path')
            ->with(BladeFacade::base_path('storage/views'))
            ->reply('tests/cache');
    }

    public function tearDown(): void
    {
        \WP_Mock::tearDown();
        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Blade::class, $this->blade);
    }

    public function testDefaults()
    {
        $this->assertEquals(
            'tests/views',
            $this->blade->viewPaths
        );

        $this->assertEquals(
            'tests/cache/1',
            $this->blade->cachePath
        );

        $this->assertEquals(
            true,
            $this->blade->createCacheDirectory
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
            trim(blade_controller('variable', 'Variable', false))
        );
    }

    public function testCreateCacheDirectory()
    {
        $this->assertDirectoryExists('tests/cache/1');
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
