<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(2);

$t->comment('nbFileSystemUtils - Test sanitize_dir');


$t->is(nbFileSystemUtils::sanitize_dir("/foo/bar/"), "/foo/bar", 'nbFileSystemUtils::sanitize_dir() returns a dir path without a trailing slash');
$t->is(nbFileSystemUtils::sanitize_dir("/foo/bar"), "/foo/bar", 'nbFileSystemUtils::sanitize_dir() returns a dir path unchanged');
