<?php

require_once dirname(__FILE__) . '/../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../lib/core/autoload/nbAutoload.php';

$basedir = dirname(__FILE__);

$autoload = nbAutoload::getInstance();
$autoload->register();

$autoload->addDirectory('vendor/', '*.php', true);
$autoload->addDirectory('lib/', '*.php', true);
$autoload->addDirectory('test/lib/', '*.php', true);

$output = new nbConsoleOutput();
$output->setFormatter(new nbAnsiColorFormatter());
$logger = nbLogger::getInstance();
$logger->setOutput($output);
