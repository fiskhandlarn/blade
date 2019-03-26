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
}
