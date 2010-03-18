<?php

require_once dirname(__FILE__) . '/../../../../../test/bootstrap/unit.php';

$t = new lime_test(14);

$parser = new nbVsProjParser(dirname(__FILE__) . '/../../data/Twimm.vcproj');
$t->comment('nbVsProjParserTest - Test ');
$t->is($parser->getProjectName(),
  'Twimm',
  '->getProjectName() returns "Twimm"');

$t->comment('nbVsProjParserTest - Test get project bin directory');
$t->is($parser->getProjectBinDir(nbVsProjParser::DEBUG),
  'bin\Debug',
  '->getProjectBinDir() returns "bin\Debug"');
$t->is($parser->getProjectBinDir(nbVsProjParser::RELEASE),
  'bin\Release',
  '->getProjectBinDir() returns "bin\Release"');

$t->comment('nbVsProjParserTest - Test get project obj directory');
$t->is($parser->getProjectObjDir(nbVsProjParser::DEBUG),
  'obj\Debug',
  '->getProjectObjDir() returns "obj\Debug"');
$t->is($parser->getProjectObjDir(nbVsProjParser::RELEASE),
  'obj\Release',
  '->getProjectObjDir() returns "obj\Release"');

$t->comment('nbVsProjParserTest - Test get project defines');
$t->is($parser->getProjectDefines(nbVsProjParser::DEBUG),
  array('WIN32', '_DEBUG', '_CONSOLE', 'BOOST_ALL_DYN_LINK', 'CEGUI_STATIC', 'TOLUA_STATIC'),
  '->getProjectDefines() returns an array with preprocessor definitions');
$t->is($parser->getProjectDefines(nbVsProjParser::RELEASE),
  array('WIN32', '_CONSOLE', 'BOOST_ALL_DYN_LINK', 'CEGUI_STATIC', 'TOLUA_STATIC'),
  '->getProjectDefines() returns an array with preprocessor definitions');

$t->comment('nbVsProjParserTest - Test get project includes');
$includesDebug = array(
  'include',
  '..\libs\TwimmAppLib\include',
  '..\libs\Module\include',
  '..\libs\Foundation\include',
  '..\libs\Configuration\include',
  '..\libs\WorldPlugin\include',
  '..\libs\Engine3D\include',
  '..\libs\WorldModule\include',
  '"$(IRRLICHT_HOME)/include"'
);
$t->is($parser->getProjectIncludes(nbVsProjParser::DEBUG),
  $includesDebug,
  '->getProjectIncludes() returns an array with include paths');

$includesRelease = array(
  'include',
  '..\libs\TwimmAppLib\include',
  '..\libs\ModuleLib\include',
  '..\libs\FoundationLib\include',
  '..\libs\ConfigurationModule\include',
  '..\libs\WorldPlugin\include',
  '..\libs\Engine3D\include',
  '..\libs\WorldModule\include',
  '..\libs\WorldModuleExtra\include',
  '"$(IRRLICHT_HOME)/include"'
);
$t->is($parser->getProjectIncludes(nbVsProjParser::RELEASE),
  $includesRelease,
  '->getProjectIncludes() returns an array with include paths');

$t->comment('nbVsProjParserTest - Test get project sources');
$sources = array(
  '.\main.cpp',
  '.\src\Version.cpp'
);
$t->is($parser->getProjectSources(), $sources, '->getProjectSources() returns an array with source files');

$t->comment('nbVsProjParserTest - Test get project lib paths');
$libpathsDebug = array(
  '"..\libs\Debug"',
  '"$(IRRLICHT_HOME)\lib\Win32-visualstudio"',
  '"$(OIS_HOME)\lib"'
);
$t->is($parser->getProjectLibPaths(nbVsProjParser::DEBUG),
  $libpathsDebug,
  '->getProjectLibPaths() returns an array with project lib paths');

$libpathsRelease = array(
  '"..\libs\Release"',
  '"$(IRRLICHT_HOME)\lib\win32-visualStudio"',
  '"$(OIS_HOME)\lib"'
);
$t->is($parser->getProjectLibPaths(nbVsProjParser::RELEASE),
  $libpathsRelease,
  '->getProjectLibPaths() returns an array with project lib paths');

$t->comment('nbVsProjParserTest - Test get project libs');
$libsDebug = array(
  'TwimmAppLib.lib',
  'Foundation.lib',
  'Module.lib',
  'IrrlichtLib.lib',
  'Irrlicht_d.lib',
  'WorldModule.lib',
  'winmm.lib'
);
$t->is($parser->getProjectLibs(nbVsProjParser::DEBUG),
  $libsDebug,
  '->getProjectLibs() returns an array with project libs');

$libsRelease = array(
  'TwimmAppLib.lib',
  'FoundationLib.lib',
  'IrrlichtLib.lib',
  'Irrlicht.lib',
  'WorldModule.lib',
  'winmm.lib'
);
$t->is($parser->getProjectLibs(nbVsProjParser::RELEASE),
  $libsRelease,
  '->getProjectLibs() returns an array with project libs');
