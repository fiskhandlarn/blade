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
        \WP_Mock::setUp();

        $this->blade = new Blade('tests/views', 'tests/cache');

        \WP_Mock::onFilter('blade/view/paths')
            ->with(base_path('resources/views'))
            ->reply('tests/views');

        \WP_Mock::onFilter('blade/cache/path')
            ->with(base_path('storage/views'))
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
        $this->blade->cleanCacheDirectory();

        $this->assertEquals(
            'A new life awaits you in the Off-world colonies!',
            trim($this->blade->render('plain'))
        );

        $this->blade->cleanCacheDirectory();

        $this->assertEquals(
            'A new life awaits you in the Off-world colonies!',
            trim(blade('plain', [], false))
        );
    }

    public function testCapillaryDilation()
    {
        $this->blade->cleanCacheDirectory();

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim($this->blade->render('variable', ['machine' => 'Voight-Kampff']))
        );

        $this->blade->cleanCacheDirectory();

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim(blade('variable', ['machine' => 'Voight-Kampff'], false))
        );
    }

    public function testCreateCacheDirectory()
    {
        $this->assertDirectoryExists('tests/cache/1');
    }

    public function testDirective()
    {
        $this->blade->cleanCacheDirectory();

        $this->blade->directive('datetime', function ($expression) {
            return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
        });

        $this->assertEquals(
            '2019-11-01 00:02:42',
            trim($this->blade->render('directive'))
        );

        $this->blade->cleanCacheDirectory();

        blade_directive('datetime', function ($expression) {
            return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
        });

        $this->assertEquals(
            '2019-11-01 00:02:42',
            trim(blade('directive', [], false))
        );
    }

    public function testComposer()
    {
        $this->blade->cleanCacheDirectory();

        $this->blade->composer('composer', function ($view) {
            $view->with(['badge' => 'B26354']);
        });

        $this->assertEquals(
            'Deckard. B26354.',
            trim($this->blade->render('composer'))
        );

        $this->blade->cleanCacheDirectory();

        blade_composer('composer', function ($view) {
            $view->with(['badge' => 'B26354']);
        });

        $this->assertEquals(
            'Deckard. B26354.',
            trim(blade('composer', [], false))
        );
    }

    public function testShare()
    {
        $this->blade->cleanCacheDirectory();

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

        $this->blade->cleanCacheDirectory();

        // shorthand
        blade_share('position', '45');

        $this->assertEquals(
            'Track 45 right. Stop. Center and stop.',
            trim(blade('share-shorthand', [], false))
        );

        // array
        blade_share([
            'startPosition' => '224',
            'endPosition' => '176',
        ]);

        $this->assertEquals(
            'Enhance 224 to 176.',
            trim(blade('share-array', [], false))
        );
    }
}
