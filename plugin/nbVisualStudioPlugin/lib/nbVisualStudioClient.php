<?php

class nbVisualStudioClient
{
  const LIB = 1;
  const DLL = 2;
  const APP = 4;

  const DEBUG = 'Debug';
  const RELEASE = 'Release';

  const OPT_INCREMENTAL = 1;
  const OPT_MULTIPROC = 2;

  public $stdCppExtension = 'cpp';
  public $stdObjExtension = 'obj';

  private $outputName;
  private $projectType;
  private $configuration;
  private $projectDefines = '';
  private $projectIncludes = '';
  private $projectLibs = '';

  private $compilerFlags = '';
  private $compilerDefines = '';
  private $linkerFlags = '';

  private $intermediateBaseDir = 'obj';
  private $intermediateDir = '';
  private $outputBaseDir = 'bin';
  private $outputDir = '';
  

  // $conf [Debug|Release]

  // outputDir (string) = bin/$conf
  // intermediateDir (string) = obj/$conf
  // type [console|lib|dll]
  // useUnicode (bool)
  // warningLevel
  //

  //      \ Configuration  Debug   Release     Both
  //
  // Compiler option
  //
  // use unicode                                (/DUNICODE /D_UNICODE)
  //
  // Optimization           /Od     /O2
  // Intrinsic funct.s              /Oi
  // Whole prog opt.                /GL
  //
  // Define                 _DEBUG  NDEBUG
  //
  // Minimal rebuild        /Gm
  // C++ exceptions                             (/EHsc)
  // runtime checks         /RTC1
  // runtime lib            /MDd    /MD
  // function-level link            /Gy
  //
  // calling                                    (/Gd)
  // compile as                                 (/TP)
  //
  // Linker option
  //
  // Incremental            /INCREMENTAL  /INCREMENTAL:NO
  // generate debug info    /DEBUG
  // Subsystem                                  (/SUBSYSTEM:[CONSOLE|WINDOWS])
  // optimizations                        /OPT:REF /OPT:ICF /ltcg
  // 

  public function  __construct($outputName, $type = self::LIB, $conf = self::DEBUG)
  {
    $this->outputName = $outputName;
    $this->setConfiguration($type, $conf);
  }

  public function setOption($option, $value = null)
  {
    switch ($option)
    {
      case self::OPT_INCREMENTAL:
      if ($value === true)
      {
        $this->compilerFlags .= ' /Gm';
        $this->linkerFlags .= ' /INCREMENTAL';
      }
      break;
      case self::OPT_MULTIPROC:
      if ($value > 1)  // not compatible with incremental
        $this->compilerFlags .= " /MP$value";
      elseif ($value == 0)
        $this->compilerFlags .= " /MP"; // use as many prosessors as possible
    }
  }

  public function setProjectDefines(array $defines)
  {
    $this->projectDefines = '';
    foreach ($defines as $define)
      $this->projectDefines .= " /D$define";
  }

  public function setProjectIncludes(array $includes)
  {
    $this->projectIncludes = '';
    foreach ($includes as $include)
      $this->projectIncludes .= " /I$include";
  }

  public function setProjectLibraries(array $libs)
  {
    $this->projectLibs = '';
    foreach ($libs as $lib)
      $this->projectLibs .= " /L$lib";
  }

  public function getCompilerCmdLine()
  {
    $cmdLine = "cl /c /nologo";
    if ($this->compilerFlags != '')
      $cmdLine .= $this->compilerFlags;
    if ($this->compilerDefines != '')
      $cmdLine .= $this->compilerDefines;
    if ($this->projectDefines != '')
      $cmdLine .= $this->projectDefines;
    if ($this->projectIncludes != '')
      $cmdLine .= $this->projectIncludes;

    return $cmdLine;
  }

  public function getLinkerCmdLine()
  {
    if ($this->projectType == self::LIB)
    {
      $cmdLine = "lib /nologo";
    }
    else // DLL or APP
    {
      $cmdLine = "link /nologo";
      if ($this->linkerFlags != '')
        $cmdLine .= $this->linkerFlags;
      if ($this->projectLibs != '')
        $cmdLine .= $this->projectLibs;
    }
    $cmdLine .= " /OUT:$this->outputName";

    // From <http://msdn.microsoft.com/en-us/library/hx5b050y%28VS.80%29.aspx>
    // To pass a file to the linker, specify the filename on the command line after the LINK command.
    // You can specify an absolute or relative path with the filename, and you can use wildcards in the filename.
    $cmdLine .= " obj/$this->configuration/*.$this->stdObjExtension";
    return $cmdLine;
  }

  private function setConfiguration($type, $conf)
  {
    $this->projectType = $type;
    $this->configuration = $conf;
    $this->compilerDefines = ' /DUNICODE /D_UNICODE /DWIN32';
    $this->compilerFlags = ' /EHsc /Gd /TP';
    $this->linkerFlags = '';
    $this->intermediateDir = "$this->intermediateBaseDir/$this->configuration";
    $this->outputDir = "$this->outputBaseDir/$this->configuration";
    if ($conf == self::DEBUG) {
      $this->compilerDefines .= ' /D_DEBUG';
      $this->compilerFlags .= ' /Od /RTC1 /MDd';
    }
    else {
      $this->compilerDefines .= ' /DNDEBUG';
      $this->compilerFlags .= ' /O2 /Oi /GL /MD /Gy';
    }
  }

//  private function getFileList($pattern)
//  {
//    $objFiles = glob($pattern);
//    $files = '';
//    foreach ($objFiles as $file)
//      $files .= " $file";
//    return $files;
//  }
}
