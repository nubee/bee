<?php

class nbBeeInstallCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('bee:install')
      ->setBriefDescription('Installs bee')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('install-dir', nbArgument::REQUIRED, 'Installation directory')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('source-dir', 's', nbOption::PARAMETER_REQUIRED, 'Source directory'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $fs = $this->getFileSystem();
    
    $dest = $fs->sanitizeDirectory($arguments['install-dir']);
    $source = $fs->sanitizeDirectory(isset($options['source-dir']) ? $options['source-dir'] : './');

    if(!file_exists($dest))
      $fs->mkdir($dest, true);

    $this->logLine('Installing bee on: ' . $dest, nbLogger::COMMENT);
    
    $finder = nbFileFinder::create('any');
    
    $fs->mirror($source, $dest, $finder);
    
    try {
      $shell = new nbShell();
      if(PHP_OS == "Linux") {
        if(file_exists('/usr/bin/bee'))
          $shell->execute('rm /usr/bin/bee');
        $shell->execute('ln -s ' . $dest . '/bee /usr/bin/bee');
      }
      else if(PHP_OS == "WINNT") {
        $this->logLine('Remember to add ' . $dest . ' to your Path enviroment variable', nbLogger::COMMENT);
      }
      else
        throw new Exception("Operating System not supported");
      
      $this->logLine('Bee successfully installed', nbLogger::COMMENT);
    }
    catch(Exception $e) {
      $this->logLine('Error installing bee: ' . $e->getMessage(), nbLogger::COMMENT);
      throw $e;
    }
  }
/*
  protected function getDefaultInstallDir()
  {
    if(PHP_OS == "Linux") {
      return "/var/source/bee";
    }
    else if(PHP_OS == "WINNT") {
      return "%ProgramFiles%/bee";
    }
    else
      throw new Exception("Operating System not supported");
  }
*/
}