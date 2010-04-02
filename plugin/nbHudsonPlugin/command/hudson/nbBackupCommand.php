<?php

/**
 * Backups up an instance of Hudson CI server.
 *
 * @package    hudson
 * @subpackage command
 */
class nbBackupCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('hudson:backup')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('hudsonHome', nbArgument::REQUIRED, 'The hudson HOME directory'),
        new nbArgument('backupHome', nbArgument::REQUIRED, 'The backup target directory')
      )))
      ->setBriefDescription('Backups a hudson instance')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command creates a backup copy of a hudson server:

   <info>./bee {$this->getFullName()}</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $hudsonHome = $arguments['hudsonHome'];
    $backupHome = $arguments['backupHome'];
    $this->log("Starting backup procedure...\n", nbLogger::COMMENT);
    $this->log('From ' . $hudsonHome . ' to ' . $backupHome . "\n", nbLogger::COMMENT);
    if (!is_dir($hudsonHome))
    {
      $this->log('Cannot find hudson home directory: ' . $hudsonHome . "\n", nbLogger::COMMENT);
      return false;
    }
    nbFileSystem::mkdir($backupHome, true);

    $finder = nbFileFinder::create();
    $jobDirs = $finder->setType('dir')->relative()->add('*')->in($hudsonHome . '/jobs');
    print_r($jobDirs);
    foreach ($jobDirs as $dir) {
      nbFileSystem::mkdir($backupHome . '/jobs/'. $dir, true);
    }

    $files = $finder->setType('file')->relative()->add('*.xml')->in($hudsonHome);
    print_r($files);
    foreach ($files as $file) {
      nbFileSystem::copy($hudsonHome . '/' . $file, $backupHome . '/' . $file);
    }

    return true;
  }
}
