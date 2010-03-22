<?php

require_once dirname(__FILE__) . '/../../../../../test/bootstrap/unit.php';

$t = new lime_test(17);

putenv('TEST_ENV=C:/test/env');
putenv('TEST_ENV_2=C:/test/env2');

$parser = new nbVcProjParser(dirname(__FILE__) . '/../../data/Twimm.vcproj');
$t->comment('nbVcProjParserTest - Test ');
$t->is($parser->getName(),
  'Twimm',
  '->getName() returns "Twimm"');

$t->comment('nbVcProjParserTest - Test get project bin directory');
$parser->setConfiguration(nbVcProjParser::DEBUG);
$t->is($parser->getBinDir(),
  'bin/Debug',
  '->getBinDir() returns "bin/Debug"');
$parser->setConfiguration(nbVcProjParser::RELEASE);
$t->is($parser->getBinDir(),
  'bin/Release',
  '->getBinDir() returns "bin/Release"');

$t->comment('nbVcProjParserTest - Test get project obj directory');
$parser->setConfiguration(nbVcProjParser::DEBUG);
$t->is($parser->getObjDir(),
  'obj/Debug',
  '->getObjDir() returns "obj/Debug"');
$parser->setConfiguration(nbVcProjParser::RELEASE);
$t->is($parser->getObjDir(),
  'obj/Release',
  '->getObjDir() returns "obj/Release"');

$t->comment('nbVcProjParserTest - Test get project defines');
$parser->setConfiguration(nbVcProjParser::DEBUG);
$t->is($parser->getDefines(),
  array('WIN32', '_DEBUG', '_CONSOLE', 'BOOST_ALL_DYN_LINK', 'CEGUI_STATIC', 'TOLUA_STATIC'),
  '->getDefines() returns an array with preprocessor definitions');
$parser->setConfiguration(nbVcProjParser::RELEASE);
$t->is($parser->getDefines(),
  array('WIN32', '_CONSOLE', 'BOOST_ALL_DYN_LINK', 'CEGUI_STATIC', 'TOLUA_STATIC'),
  '->getDefines() returns an array with preprocessor definitions');

$t->comment('nbVcProjParserTest - Test get project includes');
$includesDebug = array(
  'include',
  '../libs/TwimmAppLib/include',
  '../libs/Module/include',
  '../libs/Foundation/include',
  '../libs/Configuration/include',
  '../libs/WorldPlugin/include',
  '../libs/Engine3D/include',
  '../libs/WorldModule/include',
  'C:/test/env/include'
);
$parser->setConfiguration(nbVcProjParser::DEBUG);
$t->is($parser->getIncludes(),
  $includesDebug,
  '->getIncludes() returns an array with include paths');

$includesRelease = array(
  'include',
  '../libs/TwimmAppLib/include',
  '../libs/ModuleLib/include',
  '../libs/FoundationLib/include',
  '../libs/ConfigurationModule/include',
  '../libs/WorldPlugin/include',
  '../libs/Engine3D/include',
  '../libs/WorldModule/include',
  '../libs/WorldModuleExtra/include',
  'C:/test/env/include'
);
$parser->setConfiguration(nbVcProjParser::RELEASE);
$t->is($parser->getIncludes(),
  $includesRelease,
  '->getIncludes() returns an array with include paths');

$t->comment('nbVcProjParserTest - Test get project sources');
$sources = array(
  './main.cpp',
  './src/Version.cpp'
);
$t->is($parser->getSources(), $sources, '->getSources() returns an array with source files');

$t->comment('nbVcProjParserTest - Test get project lib paths');
$libpathsDebug = array(
  '../libs/Debug',
  'C:/test/env/lib/Win32-visualstudio',
  'C:/test/env2/lib'
);
$parser->setConfiguration(nbVcProjParser::DEBUG);
$t->is($parser->getLibPaths(),
  $libpathsDebug,
  '->getLibPaths() returns an array with project lib paths');

$libpathsRelease = array(
  '../libs/Release',
  'C:/test/env/lib/win32-visualStudio',
  'C:/test/env2/lib'
);
$parser->setConfiguration(nbVcProjParser::RELEASE);
$t->is($parser->getLibPaths(),
  $libpathsRelease,
  '->getLibPaths() returns an array with project lib paths');

$t->comment('nbVcProjParserTest - Test get project libs');
$libsDebug = array(
  'TwimmAppLib.lib',
  'Foundation.lib',
  'Module.lib',
  'IrrlichtLib.lib',
  'Irrlicht_d.lib',
  'WorldModule.lib',
  'winmm.lib'
);
$parser->setConfiguration(nbVcProjParser::DEBUG);
$t->is($parser->getLibs(),
  $libsDebug,
  '->getLibs() returns an array with project libs');

$libsRelease = array(
  'TwimmAppLib.lib',
  'FoundationLib.lib',
  'IrrlichtLib.lib',
  'Irrlicht.lib',
  'WorldModule.lib',
  'winmm.lib'
);
$parser->setConfiguration(nbVcProjParser::RELEASE);
$t->is($parser->getLibs(),
  $libsRelease,
  '->getLibs() returns an array with project libs');

$t->comment('nbVcProjParserTest - Test expand visual studio macros');
$parser = new nbVcProjParser(dirname(__FILE__) . '/../../data/Twimm.vcproj');
$string = 'include;$(SolutionDir)/$(ConfigurationName);bin/$(ConfigurationName)';
$t->is($parser->expandMacro($string),
  'include;' . getcwd() . '/Debug;bin/Debug',
  '->expandMacro() returns a string with expanded visual studio macros');
$parser->setConfiguration(nbVcProjParser::RELEASE);
$t->is($parser->expandMacro($string),
  'include;' . getcwd() . '/Release;bin/Release',
  '->expandMacro() returns a string with expanded visual studio macros');

$t->comment('nbVcProjParserTest - Test expand env variables');
$parser = new nbVcProjParser(dirname(__FILE__) . '/../../data/Twimm.vcproj');
$string = 'a/path;$(TEST_ENV);$(TEST_ENV_2)/lib';
$t->is($parser->expandEnvVariable($string),
  'a/path;' . getenv('TEST_ENV') . ';' . getenv('TEST_ENV_2') . '/lib',
  '->expandEnvVariable() returns a string with expanded env variables');
