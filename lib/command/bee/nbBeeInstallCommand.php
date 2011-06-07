<?php

class nbBeeInstallCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:install')
            ->setBriefDescription('Install bee')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('source_dir', nbArgument::OPTIONAL, 'Source directory','.'),
                new nbArgument('install_dir', nbArgument::OPTIONAL, 'Installation directory',$this->getDefaultInstallDir())
            )));

    $this->setOptions(new nbOptionSet(array(
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $installDir = nbFileSystemUtils::sanitize_dir($arguments['install_dir']);
    $sourceDir =  nbFileSystemUtils::sanitize_dir($arguments['source_dir']);

    if (!file_exists($installDir))
      nbFileSystem::mkdir($installDir,true);

    $this->logLine('Installing bee on folder ' . $installDir, nbLogger::COMMENT);
    try{
      $shell = new nbShell();
      if(PHP_OS == "Linux"){
        $shell->execute( 'cp -rf '.$sourceDir.'/* '.$installDir.'/');
        $shell->execute( 'ln -s '.$installDir.'/bee /usr/bin/bee');
      }
      else if(PHP_OS == "WINNT"){
        if ($sourceDir == '.' ) $sourceDir = '*';
        $shell->execute( 'xcopy '.$sourceDir.' '.$installDir.'\ /E /Y');
        $this->logLine('Remember to add '.$installDir.' to your Path enviroment variable', nbLogger::COMMENT);
      }
      else
        throw new Exception("Operating System not supported");
      $this->logLine('Bee successfully installed', nbLogger::COMMENT);
    }
    catch(Exception $e){
      $this->logLine('Error installing bee: '.$e->getMessage(), nbLogger::COMMENT); 
      throw $e;
    }
  }

  protected function getDefaultInstallDir() {
      if(PHP_OS == "Linux"){
        return "/var/source/bee";
      }
      else if(PHP_OS == "WINNT"){
        return "%ProgramFiles%\bee";
      }
      else
        throw new Exception("Operating System not supported");
  }
}