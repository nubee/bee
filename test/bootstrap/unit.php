<?php

require_once dirname(__FILE__) . '/../../lib/core/autoload/nbAutoload.php';

$basedir = dirname(__FILE__);

$autoload = nbAutoload::getInstance();
$autoload->register();
$autoload->addDirectory('lib/vendor/', '*.php', true);
$autoload->addDirectory('lib/', '*.class.php', true);
$autoload->addDirectory('tests/lib/', '*.class.php', true);
