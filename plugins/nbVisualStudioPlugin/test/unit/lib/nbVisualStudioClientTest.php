<?php

require_once dirname(__FILE__) . '/../../../../../test/bootstrap/unit.php';

$t = new lime_test(23);

$serviceContainer->pluginLoader->loadPlugins(array('nbVisualStudioPlugin'));

$t->comment('nbVisualStudioClientTest - Test default compiler line for LIB DEBUG project');
$client = new nbVisualStudioClient('outputFile');
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG',
        '->getCompilerCmdLine() has all default flags for LIB + DEBUG configuration');
$t->is($client->getLinkerCmdLine(),
        'lib /nologo /OUT:outputFile obj/Debug/*.obj',
        '->getLinkerCmdLine() has all default flags for LIB + DEBUG configuration');

$t->comment('nbVisualStudioClientTest - Test default compiler line for LIB RELEASE project');
$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::LIB, nbVisualStudioClient::RELEASE);
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /O2 /Oi /GL /MD /Gy /DUNICODE /D_UNICODE /DWIN32 /DNDEBUG',
        '->getCompilerCmdLine() has all default flags for LIB + RELEASE configuration');
$t->is($client->getLinkerCmdLine(),
        'lib /nologo /OUT:outputFile obj/Release/*.obj',
        '->getLinkerCmdLine() has all default flags for LIB + RELEASE configuration');

$t->comment('nbVisualStudioClientTest - Test default compiler line for APP DEBUG project');
$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG',
        '->getCompilerCmdLine() has all default flags for APP + DEBUG configuration');
$t->is($client->getLinkerCmdLine(),
        'link /nologo /OUT:outputFile obj/Debug/*.obj',
        '->getLinkerCmdLine() has all default flags for APP + DEBUG configuration');

$t->comment('nbVisualStudioClientTest - Test default compiler line for APP RELEASE project');
$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP, nbVisualStudioClient::RELEASE);
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /O2 /Oi /GL /MD /Gy /DUNICODE /D_UNICODE /DWIN32 /DNDEBUG',
        '->getCompilerCmdLine() has all default flags for APP + RELEASE configuration');
$t->is($client->getLinkerCmdLine(),
        'link /nologo /OUT:outputFile obj/Release/*.obj',
        '->getLinkerCmdLine() has all default flags for APP + RELEASE configuration');


$t->comment('nbVisualStudioClientTest - Test set additional defines to compiler');
$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$client->setProjectDefines(array('CUSTOM'));
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG /DCUSTOM',
        '->setProjectDefines() sets one additional define to compiler command line');
$client->setProjectDefines(array('CUSTOM1', 'CUSTOM2'));
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG /DCUSTOM1 /DCUSTOM2',
        '->setProjectDefines() sets all additional defines to compiler command line');

$t->comment('nbVisualStudioClientTest - Test set additional includes to compiler');
$client = new nbVisualStudioClient('outputFile');
$client->setProjectIncludes(array('include1'));
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG /I"include1"',
        '->setProjectIncludes() sets one additional include to compiler command line');
$client->setProjectIncludes(array('include1', 'include2'));
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG /I"include1" /I"include2"',
        '->setProjectIncludes() sets al additional includes to compiler command line');

$t->comment('nbVisualStudioClientTest - Test set sources to compiler');
$client = new nbVisualStudioClient('outputFile');
$client->setProjectSources(array('src1'));
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG "src1"',
        '->setProjectSources() sets one source to compiler command line');
$client->setProjectSources(array('src1', 'src2'));
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG "src1" "src2"',
        '->setProjectSources() sets al additional includes to compiler command line');

$t->comment('nbVisualStudioClientTest - Test set additional lib paths to linker');
$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$client->setProjectLibPaths(array('lib/path/1'));
$t->is($client->getLinkerCmdLine(),
        'link /nologo /LIBPATH:"lib/path/1" /OUT:outputFile obj/Debug/*.obj',
        '->setProjectLibPaths() sets one additional lib path to linker command line');

$client->setProjectLibPaths(array('lib/path/1', 'lib/path/2'));
$t->is($client->getLinkerCmdLine(),
        'link /nologo /LIBPATH:"lib/path/1" /LIBPATH:"lib/path/2" /OUT:outputFile obj/Debug/*.obj',
        '->setProjectLibPaths() sets all additional lib paths to linker command line');

$t->comment('nbVisualStudioClientTest - Test set additional libs to linker');
$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$client->setProjectLibs(array('lib1'));
$t->is($client->getLinkerCmdLine(),
        'link /nologo "lib1" /OUT:outputFile obj/Debug/*.obj',
        '->setProjectLibs() sets one additional lib to linker command line');

$client->setProjectLibs(array('lib1', 'lib2'));
//$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$t->is($client->getLinkerCmdLine(),
        'link /nologo "lib1" "lib2" /OUT:outputFile obj/Debug/*.obj',
        '->setProjectLibs() sets all additional libs to linker command line');


$t->comment('nbVisualStudioClientTest - Test set incremental option');
$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$client->setOption(nbVisualStudioClient::OPT_INCREMENTAL, true);
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /Gm /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG',
        '->getCompilerCmdLine() has /Gm option for incremental compiling');
$t->is($client->getLinkerCmdLine(),
        'link /nologo /INCREMENTAL /OUT:outputFile obj/Debug/*.obj',
        '->getLinkerCmdLine() has /INCREMENTAL option for incremental linking');

$t->comment('nbVisualStudioClientTest - Test set multiprocess option');
$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$client->setOption(nbVisualStudioClient::OPT_MULTIPROC, 1);
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG',
        '->getCompilerCmdLine() has no /MP option for multiprocess build');

$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$client->setOption(nbVisualStudioClient::OPT_MULTIPROC, 2);
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /MP2 /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG',
        '->getCompilerCmdLine() has /MP2 option for 2-processes build');

$client = new nbVisualStudioClient('outputFile', nbVisualStudioClient::APP);
$client->setOption(nbVisualStudioClient::OPT_MULTIPROC, 0);
$t->is($client->getCompilerCmdLine(),
        'cl /c /nologo /EHsc /Gd /TP /Od /RTC1 /MDd /MP /DUNICODE /D_UNICODE /DWIN32 /D_DEBUG',
        '->getCompilerCmdLine() has /MP option for multiprocesses build');
