<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addParameters([
    'cipherKey' => 'PleaseChangeMe'
]);

$configurator->addConfig(__DIR__ . '/config/config.neon');

$container = $configurator->createContainer();

return $container;
