<?php

class nbIvyRetrieveCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('ivy:retrieve')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('ivyfile', nbArgument::OPTIONAL, 'Ivy file', 'ivy.xml')
      )))
      ->setBriefDescription('Retrieves dependencies from the given ivy file')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> retrieves dependencies:

    <info>./bee {$this->getFullName()} ivyfile</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    nbFileSystem::rmdir(nbConfig::get('project_dependencies'), true);

    $client = new nbIvyClient();

    $this->logLine('Retrieving dependencies...', nbLogger::COMMENT);
    $command = $client->getRetrieveCmdLine();

    $this->executeShellCommand($command);

    $finder = nbFileFinder::create('file');
    $files = $finder->add('*-api.zip')->in(nbConfig::get('project_dependencies'));
    
    $zip = new ZipArchive();
    foreach ($files as $file) {
      if ($zip->open($file, ZIPARCHIVE::CHECKCONS) !== true)
        throw new Exception('Error opening zip file ' . $file);

      $zip->extractTo(dirname($file));
      $zip->close();
      $this->logLine(sprintf('Unzipping %s', $file), nbLogger::COMMENT);
    }

    $files = $finder->add('*.zip')->in(nbConfig::get('project_dependencies'));
    foreach ($files as $file)
      nbFileSystem::delete($file);
  }
}
