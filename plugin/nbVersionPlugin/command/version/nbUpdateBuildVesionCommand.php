<?php

class nbUpdateBuildVesionCommand extends nbCommand {

  protected function configure() {
    $this->setName('version:update-build')
            ->setBriefDescription('')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('version-file', nbArgument::OPTIONAL, 'Version file','version.yml'),
            )));

    $this->setOptions(new nbOptionSet(array(
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    if (!file_exists($arguments['version-file'])) 
      throw new Exception('Version file does not exist');
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($arguments['version-file']);
    $initialVersion = nbConfig::get('version');
    $arrayVersion = array();
    $arrayVersion = preg_split('/\./', $initialVersion);
    $arrayVersion[3]++;
    $finalVersion = join ('.', $arrayVersion);
    nbFileSystem::replaceTokens($initialVersion, $finalVersion, $arguments['version-file']);
    return true;
  }

}