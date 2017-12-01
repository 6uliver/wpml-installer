<?php

namespace WPMLInstaller\Test;

use Composer\Installer\PackageEvent;
use WPMLInstaller\Plugin;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    protected $plugin;

    protected function setUp()
    {
        $this->plugin = new Plugin();
    }

    public function testImplementsPluginInterface()
    {
        $this->assertInstanceOf(
            'Composer\Plugin\PluginInterface',
            $this->plugin
        );
    }

    public function testUrlCreatedCorrectly()
    {
        putenv("WPML_USER_ID=testUserId");
        putenv("WPML_SUBSCRIPTION_KEY=testSubscriptionKey");

        $package = $this
            ->getMockBuilder('Composer\Package\PackageInterface')
            ->setMethods([
                'getName',
                'getPrettyVersion',
                'getDistUrl',
                'setDistUrl'
            ])
            ->getMockForAbstractClass();

        $package
            ->expects($this->once())
            ->method('getName')
            ->willReturn("wpml/multilingual-cms");
        $package
            ->expects($this->once())
            ->method('setDistUrl')
            ->with('https://wpml.org/' .
                '?download=6088' .
                '&user_id=testUserId' .
                '&subscription_key=testSubscriptionKey' .
                '&version=1.2.3.4');
        $package
            ->expects($this->once())
            ->method('getPrettyVersion')
            ->willReturn("1.2.3.4");

        $operationClass =
            'Composer\DependencyResolver\Operation\UpdateOperation';
        $operation = $this
            ->getMockBuilder($operationClass)
            ->disableOriginalConstructor()
            ->setMethods(['getJobType', 'getTargetPackage'])
            ->getMock();
        $operation
            ->expects($this->once())
            ->method('getJobType')
            ->willReturn('update');
        $operation
            ->expects($this->once())
            ->method('getTargetPackage')
            ->willReturn($package);

        $packageEvent = $this
            ->getMockBuilder('Composer\Installer\PackageEvent')
            ->disableOriginalConstructor()
            ->setMethods(['getOperation'])
            ->getMock();
        $packageEvent
            ->expects($this->once())
            ->method('getOperation')
            ->willReturn($operation);

        $this->plugin->updateDistUrl($packageEvent);
    }
}
