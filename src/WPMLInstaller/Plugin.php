<?php

namespace WPMLInstaller;

use Composer\Composer;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    const WPML_PACKAGE_NAME = "wpml/multilingual-cms";
    const WPML_BASE_URL = "https://wpml.org/";
    const WPML_DOWNLOAD_ID = "6088";

    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::PRE_PACKAGE_INSTALL => 'updateDistUrl'
        ];
    }

    protected static function getPackage(OperationInterface $operation)
    {
        if ($operation->getJobType() === 'update') {
            return $operation->getTargetPackage();
        } else {
            return $operation->getPackage();
        }
    }

    public function updateDistUrl(PackageEvent $event)
    {
        $package = self::getPackage($event->getOperation());

        if ($package->getName() === self::WPML_PACKAGE_NAME) {
            $package->setDistUrl($this->getDistUrl($package));
        }
    }

    protected function getUserId()
    {
        return 'dummyUserId';
    }

    protected function getSubscriptionKey()
    {
        return 'dummySubscriptionKey';
    }

    protected function getDistUrl(PackageInterface $package)
    {
        return self::WPML_BASE_URL .
            '?download=' . self::WPML_DOWNLOAD_ID .
            '&user_id=' . $this->getUserId() .
            '&subscription_key=' . $this->getSubscriptionKey() .
            '&version=' . $package->getPrettyVersion();
    }
}
