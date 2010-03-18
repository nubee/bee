<?php

class nbVsProjParser
{
  const DEBUG = 'Debug';
  const RELEASE = 'Release';
  
  private
    $document;
  
  public function __construct($vsprojFile)
  {
    $this->document = new DOMDocument();
    $this->document->validateOnParse = true;
    $this->document->load($vsprojFile);
    //$this->xml = simplexml_load_file($vsprojFile);
  }

  public function getProjectName()
  {
    return $this->document
      ->getElementsByTagName('VisualStudioProject')
      ->item(0)
      ->getAttribute('Name');
  }

  public function getProjectBinDir($configuration)
  {
    $binDir = '';
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $configuration . '/i', $confName))
        continue;
      $binDir = $conf->getAttribute('OutputDirectory');
      $binDir = str_replace('$(ConfigurationName)', $configuration, $binDir);
    }
    return $binDir;
  }

  public function getProjectObjDir($configuration)
  {
    $objDir = '';
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $configuration . '/i', $confName))
        continue;
      $objDir = $conf->getAttribute('IntermediateDirectory');
      $objDir = str_replace('$(ConfigurationName)', $configuration, $objDir);
    }
    return $objDir;
  }

  public function getProjectDefines($configuration)
  {
    $defines = array();
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $configuration . '/i', $confName))
        continue;
      foreach($conf->getElementsByTagName('Tool') as $tool)
        if($tool->getAttribute('Name') == 'VCCLCompilerTool')
          $defines = explode(';', $tool->getAttribute('PreprocessorDefinitions'));
    }
    return $defines;
  }

  public function getProjectIncludes($configuration)
  {
    $includes = array();
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $configuration . '/i', $confName))
        continue;
      foreach($conf->getElementsByTagName('Tool') as $tool)
        if($tool->getAttribute('Name') == 'VCCLCompilerTool')
          $includes = explode(';', $tool->getAttribute('AdditionalIncludeDirectories'));
    }
    foreach($includes as $k => $v)
      $includes[$k] = str_replace('$(ConfigurationName)', $configuration, $v);
    return $includes;
  }

  public function getProjectSources()
  {
    $sources = array();
    foreach($this->document->getElementsByTagName('File') as $file) {
      $path = $file->getAttribute('RelativePath');
      if(preg_match('/^.+\.cpp$/i', $path))
        $sources[] = $path;
    }
    return $sources;
  }

  public function getProjectLibPaths($configuration)
  {
    $libpaths = array();
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $configuration . '/i', $confName))
        continue;
      foreach($conf->getElementsByTagName('Tool') as $tool)
        if($tool->getAttribute('Name') == 'VCLinkerTool')
          $libpaths = explode(';', $tool->getAttribute('AdditionalLibraryDirectories'));
    }
    foreach($libpaths as $k => $v)
      $libpaths[$k] = str_replace('$(ConfigurationName)', $configuration, $v);
    return $libpaths;
  }

  public function getProjectLibs($configuration)
  {
    $libs = array();
    foreach($this->document->getElementsByTagName('Configuration') as $conf) {
      $confName = $conf->getAttribute('Name');
      if(!preg_match('/' . $configuration . '/i', $confName))
        continue;
      foreach($conf->getElementsByTagName('Tool') as $tool)
        if($tool->getAttribute('Name') == 'VCLinkerTool')
          $libs = explode(' ', $tool->getAttribute('AdditionalDependencies'));
    }
    return $libs;
  }
}
