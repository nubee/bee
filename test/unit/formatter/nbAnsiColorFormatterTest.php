<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(3);

$formatter = new AnsiColorFormatter();
$t->is($formatter->format("test"), "\033[32;1mtest\033[0m", 'Output is colored');
$t->is($formatter->formatText("before [inside|INFO] after"), "before \033[32;1minside\033[0m after", 'Output is formatted');
$t->is($formatter->formatText("[first|COMMENT] [second|INFO]"), "\033[33mfirst\033[0m \033[32;1msecond\033[0m", 'Output is formatted twice');
