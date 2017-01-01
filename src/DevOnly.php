<?php

namespace Eimanavicius\WordPress;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

class DevOnly implements PluginInterface, EventSubscriberInterface
{

    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    public function registerWordPressCodingStandards()
    {
        if ($this->phpCodeSnifferIsNotInstalled()) {
            $this->io->writeError('<info>PHP_CodeSniffer is not installed sikipping wpcs registration</info>');
            return;
        }
        $this->io->write('<info>Registering WordPress Coding Standards with PHP_CodeSniffer</info>');
        $this->executeCommand(
            $this->composer->getEventDispatcher(),
            'phpcs',
            ['--config-set', 'installed_paths', $this->resolveWpcsPath($this->composer->getConfig())]
        );
    }

    /**
     * @param EventDispatcher $dispatcher
     * @param string $command
     * @param array $arguments
     */
    private function executeCommand(EventDispatcher $dispatcher, $command, array $arguments)
    {
        $name = '__exec_register_wpcs';
        $dispatcher->addListener($name, $command);
        $dispatcher->dispatchScript($name, true, $arguments);
    }

    /**
     * @param Config $config
     *
     * @return string
     */
    private function resolveWpcsPath(Config $config)
    {
        return $config->get('vendor-dir') . '/wp-coding-standards/wpcs';
    }

    private function phpCodeSnifferIsNotInstalled()
    {
        return (
            0 === count(
                $this
                    ->composer
                    ->getRepositoryManager()
                    ->getLocalRepository()
                    ->findPackages('squizlabs/php_codesniffer')
            )
        );
    }

    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
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
            ScriptEvents::POST_UPDATE_CMD => 'registerWordPressCodingStandards',
            ScriptEvents::POST_INSTALL_CMD => 'registerWordPressCodingStandards',
        ];
    }
}
