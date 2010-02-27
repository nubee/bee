<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(2);

$formatter = new nbAnsiColorFormatter();
$t->is($formatter->format("before <info>inside</info> after"), "before \033[32;1minside\033[0m after", 'Output is formatted');
$t->is($formatter->format("<comment>first</comment> <info>second</info>"), "\033[33mfirst\033[0m \033[32;1msecond\033[0m", 'Output is formatted twice');
