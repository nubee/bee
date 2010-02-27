<?php

require_once dirname(__FILE__) . '/../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../lib/core/autoload/nbAutoload.php';

$basedir = dirname(__FILE__);

$autoload = nbAutoload::getInstance();
$autoload->register();
$autoload->addDirectory('lib', '*.php', true);
$autoload->addDirectory('tests/lib/', '*.php', true);
