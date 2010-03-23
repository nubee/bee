<?php

class nbVcProjParser
{
  const DEBUG = 'Debug';
  const RELEASE = 'Release';
  
  private
    $document,
    $projectName,
    $debugConfig = array(),
    $releaseConfig = array();

  public function __construct($vsprojFile)
  {
    $this->document = new DOMDocument();
    $this->document->validateOnParse = true;
    $this->document->load($vsprojFile);

    $this->projectName = $this->document
      ->getElementsByTagName('VisualStudioProject')
      ->item(0)
      ->getAttribute('Name');

    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      if(preg_match('/debug/i', $conf->getAttribute('Name'))) {
//        $this->debugConfig['Compiler'] = array();
//        $this->debugConfig['Linker'] = array();
        $this->debugConfig['Compiler']['OutputDirectory'] = $conf->getAttribute('OutputDirectory');
        $this->debugConfig['Compiler']['IntermediateDirectory'] = $conf->getAttribute('IntermediateDirectory');
        $this->debugConfig['Compiler']['ConfigurationType'] = $conf->getAttribute('ConfigurationType');
        $this->debugConfig['Compiler']['CharacterSet'] = $conf->getAttribute('CharacterSet');
        $this->debugConfig['Compiler']['ManagedExtensions'] = $conf->getAttribute('ManagedExtensions');
        $this->debugConfig['Compiler']['Optimization'] = $conf->getAttribute('Optimization');
        $this->debugConfig['Compiler']['AdditionalIncludeDirectories'] = $conf->getAttribute('AdditionalIncludeDirectories');
        $this->debugConfig['Compiler']['PreprocessorDefinitions'] = $conf->getAttribute('PreprocessorDefinitions');
        $this->debugConfig['Compiler']['RuntimeLibrary'] = $conf->getAttribute('RuntimeLibrary');
        $this->debugConfig['Compiler']['UsePrecompiledHeader'] = $conf->getAttribute('UsePrecompiledHeader');
        $this->debugConfig['Compiler']['WarningLevel'] = $conf->getAttribute('WarningLevel');
        $this->debugConfig['Compiler']['DebugInformationFormat'] = $conf->getAttribute('DebugInformationFormat');
        $this->debugConfig['Linker']['AdditionalDependencies'] = $conf->getAttribute('AdditionalDependencies');
        $this->debugConfig['Linker']['LinkIncremental'] = $conf->getAttribute('LinkIncremental');
        $this->debugConfig['Linker']['AdditionalLibraryDirectories'] = $conf->getAttribute('AdditionalLibraryDirectories');
        $this->debugConfig['Linker']['GenerateDebugInformation'] = $conf->getAttribute('GenerateDebugInformation');
        $this->debugConfig['Linker']['AssemblyDebug'] = $conf->getAttribute('AssemblyDebug');
        $this->debugConfig['Linker']['TargetMachine'] = $conf->getAttribute('TargetMachine');
      }
      if(preg_match('/release/i', $conf->getAttribute('Name'))) {
        $this->releaseConfig['Compiler']['OutputDirectory'] = $conf->getAttribute('OutputDirectory');
        $this->releaseConfig['Compiler']['IntermediateDirectory'] = $conf->getAttribute('IntermediateDirectory');
        $this->releaseConfig['Compiler']['ConfigurationType'] = $conf->getAttribute('ConfigurationType');
        $this->releaseConfig['Compiler']['CharacterSet'] = $conf->getAttribute('CharacterSet');
        $this->releaseConfig['Compiler']['ManagedExtensions'] = $conf->getAttribute('ManagedExtensions');
        $this->releaseConfig['Compiler']['Optimization'] = $conf->getAttribute('Optimization');
        $this->releaseConfig['Compiler']['AdditionalIncludeDirectories'] = $conf->getAttribute('AdditionalIncludeDirectories');
        $this->releaseConfig['Compiler']['PreprocessorDefinitions'] = $conf->getAttribute('PreprocessorDefinitions');
        $this->releaseConfig['Compiler']['RuntimeLibrary'] = $conf->getAttribute('RuntimeLibrary');
        $this->releaseConfig['Compiler']['UsePrecompiledHeader'] = $conf->getAttribute('UsePrecompiledHeader');
        $this->releaseConfig['Compiler']['WarningLevel'] = $conf->getAttribute('WarningLevel');
        $this->releaseConfig['Compiler']['DebugInformationFormat'] = $conf->getAttribute('DebugInformationFormat');
        $this->releaseConfig['Linker']['AdditionalDependencies'] = $conf->getAttribute('AdditionalDependencies');
        $this->releaseConfig['Linker']['LinkIncremental'] = $conf->getAttribute('LinkIncremental');
        $this->releaseConfig['Linker']['AdditionalLibraryDirectories'] = $conf->getAttribute('AdditionalLibraryDirectories');
        $this->releaseConfig['Linker']['GenerateDebugInformation'] = $conf->getAttribute('GenerateDebugInformation');
        $this->releaseConfig['Linker']['AssemblyDebug'] = $conf->getAttribute('AssemblyDebug');
        $this->releaseConfig['Linker']['TargetMachine'] = $conf->getAttribute('TargetMachine');
      }
    }
  }

  public function setConfiguration($configuration)
  {
    $this->configuration = $configuration;
  }

  public function getName()
  {
    return $this->document
      ->getElementsByTagName('VisualStudioProject')
      ->item(0)
      ->getAttribute('Name');
  }

  public function getType()
  {
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $this->configuration . '/i', $confName))
        continue;
      switch($conf->getAttribute('ConfigurationType')) {
        case '1':
          return 'app';
        case '2':
          return 'dll';
        case '3':
          return 'lib';
        default:
          return 'app';
      }
    }
  }

  public function getCharacterSet()
  {
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $this->configuration . '/i', $confName))
        continue;
      switch($conf->getAttribute('CharacterSet')) {
        case '0':
          return 'notset';
        case '1':
          return 'unicode';
        case '2':
          return 'multibyte';
        default:
          return 'notset';
      }
    }
  }

  public function getBinDir()
  {
    $binDir = '';
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $this->configuration . '/i', $confName))
        continue;
      $binDir = $conf->getAttribute('OutputDirectory');
      $binDir = $this->expand($binDir);
    }
    return $binDir;
  }

  public function getObjDir()
  {
    $objDir = '';
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $this->configuration . '/i', $confName))
        continue;
      $objDir = $conf->getAttribute('IntermediateDirectory');
      $objDir = $this->expand($objDir);
    }
    return $objDir;
  }

  public function getDefines()
  {
    $defines = array();
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $this->configuration . '/i', $confName))
        continue;
      foreach($conf->getElementsByTagName('Tool') as $tool)
        if($tool->getAttribute('Name') == 'VCCLCompilerTool')
          $defines = explode(';', $tool->getAttribute('PreprocessorDefinitions'));
    }
    return $defines;
  }

  public function getIncludes()
  {
    $includes = array();
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $this->configuration . '/i', $confName))
        continue;
      foreach($conf->getElementsByTagName('Tool') as $tool)
        if($tool->getAttribute('Name') == 'VCCLCompilerTool') {
          $expandedIncludes = $this->expand($tool->getAttribute('AdditionalIncludeDirectories'));
          $includes = explode(';', $expandedIncludes);
        }
    }
    return $includes;
  }

  public function getSources()
  {
    $sources = array();
    foreach($this->document->getElementsByTagName('File') as $file) {
      $path = $file->getAttribute('RelativePath');
      if(preg_match('/^.+\.cpp$/i', $path))
        $sources[] = $this->expand($path);
    }
    return $sources;
  }

  public function getLibPaths()
  {
    $libpaths = array();
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $this->configuration . '/i', $confName))
        continue;
      foreach($conf->getElementsByTagName('Tool') as $tool)
        if($tool->getAttribute('Name') == 'VCLinkerTool') {
          $expandedLibpaths = $this->expand($tool->getAttribute('AdditionalLibraryDirectories'));
          $libpaths = explode(';', $expandedLibpaths);
        }
    }
    return $libpaths;
  }

  public function getLibs()
  {
    $libs = array();
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $this->configuration . '/i', $confName))
        continue;
      foreach($conf->getElementsByTagName('Tool') as $tool)
        if($tool->getAttribute('Name') == 'VCLinkerTool')
          $libs = explode(' ', $tool->getAttribute('AdditionalDependencies'));
    }
    return $libs;
  }

  public function expand($string)
  {
    $res = $string;
    $res = $this->expandMacro($res);
    $res = $this->expandEnvVariable($res);
    //TODO: expand relative path in absolute path (?)

    $res = str_replace('\\', '/', $res);
    $res = str_replace('"', '', $res);
    return $res;
  }

  public function expandMacro($string)
  {
    $vsMacros = array(
      '/\$\(ConfigurationName\)/e',
      '/\$\(SolutionDir\)/e'
    );

    $vsMacrosReplacement = array(
      "'{$this->configuration}'",
      "getcwd()"
    );
    return preg_replace($vsMacros, $vsMacrosReplacement, $string);
  }

  public function expandEnvVariable($string)
  {
    return preg_replace('/\$\((.+?)\)/e', 'getenv(\'\\1\')', $string);
  }
}
