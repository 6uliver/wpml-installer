<?php
namespace WPMLInstaller\Test;

use WPMLInstaller\Plugin;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsPluginInterface()
    {
        $this->assertInstanceOf(
            'Composer\Plugin\PluginInterface',
            new Plugin()
        );
    }
}
