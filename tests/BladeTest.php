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
use PHPUnit\Framework\TestCase;

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
        $this->blade = new Blade('tests/views', 'tests/cache');
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
        $this->assertEquals(
            'A new life awaits you in the Off-world colonies!',
            trim($this->blade->render('plain'))
        );
    }

    public function testCapillaryDilation()
    {
        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim($this->blade->render('variable', ['machine' => 'Voight-Kampff']))
        );
    }

    public function testCreateCacheDirectory()
    {
        $this->assertDirectoryExists('tests/cache/1');
    }

    public function testDirective()
    {
        $this->blade->directive('datetime', function ($expression) {
            return "<?php echo with({$expression})->format('Y-m-d H:i:s'); ?>";
        });

        $this->assertEquals(
            '2019-11-01 00:02:42',
            trim($this->blade->render('directive'))
        );
    }

    public function testComposer()
    {
        $this->blade->composer('composer', function ($view) {
            $view->with(['badge' => 'B26354']);
        });

        $this->assertEquals(
            'Deckard. B26354.',
            trim($this->blade->render('composer'))
        );
    }

    public function testShare()
    {
        // shorthand
        $this->blade->share('badge', 'B26354');

        $this->assertEquals(
            'Deckard. B26354.',
            trim($this->blade->render('composer'))
        );

        // array
        $this->blade->share(['machine' => 'Voight-Kampff']);

        $this->assertEquals(
            'We call it Voight-Kampff for short.',
            trim($this->blade->render('variable'))
        );
    }
}
