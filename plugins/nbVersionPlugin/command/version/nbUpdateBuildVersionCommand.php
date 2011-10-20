<?php

class nbUpdateBuildVersionCommand extends nbCommand {

  protected function configure() {
    $this->setName('version:update-build')
      ->setBriefDescription('Updates build version')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('version-file', nbArgument::OPTIONAL, 'Version file', 'version.yml'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $versionFile = $arguments['version-file'];
    if (!file_exists($versionFile))
      throw new Exception('Version file: ' . $versionFile . ' does not exist');
    
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($versionFile);
    
    $initialVersion = nbConfig::get('version');
    
    $arrayVersion = array();
    $arrayVersion = preg_split('/\./', $initialVersion);
    $arrayVersion[3]++;
    
    $finalVersion = join('.', $arrayVersion);
    
    nbFileSystem::replaceTokens($initialVersion, $finalVersion, $versionFile);
    
    return true;
  }

}