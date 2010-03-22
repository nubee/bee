<?php

class nbVcProjParser
{
  const DEBUG = 'Debug';
  const RELEASE = 'Release';
  
  private
    $document,
    $configuration;

  public function __construct($vsprojFile, $configuration = nbVcProjParser::DEBUG)
  {
    $this->configuration = $configuration;
    $this->document = new DOMDocument();
    $this->document->validateOnParse = true;
    $this->document->load($vsprojFile);
    //$this->xml = simplexml_load_file($vsprojFile);
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
