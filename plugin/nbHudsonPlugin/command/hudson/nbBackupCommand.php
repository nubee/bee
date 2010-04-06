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
      ->setOptions(new nbOptionSet(array(
        new nbOption('workspace', 'w', nbOption::PARAMETER_NONE, 'Specify that workspace must be included'),
        new nbOption('fingerprints', 'f', nbOption::PARAMETER_NONE, 'Specify that fingerprints must be included'),
        new nbOption('builds', 'b', nbOption::PARAMETER_NONE, 'Specify that build history must be included'),
        new nbOption('usercontent', 'u', nbOption::PARAMETER_NONE, 'Specify that user content must be included'),
        new nbOption('autofolder', 'a', nbOption::PARAMETER_NONE, 'Backup into a subfolder with current date and time')
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
    $time = strftime('%Y%m%d-%H%M%S', time());

    $hudsonHome = $arguments['hudsonHome'];
    $backupHome = $arguments['backupHome'];
    if (isset($options['autofolder']))
      $backupHome .= '/' . $time;
    $this->log("$time - Starting backup procedure...\n", nbLogger::COMMENT);
    $this->log('From ' . $hudsonHome . ' to ' . $backupHome . "\n", nbLogger::COMMENT);
    if (!is_dir($hudsonHome))
      throw new Exception('Cannot find hudson home directory: ' . $hudsonHome);

    nbFileSystem::mkdir($backupHome, true);

    $finder = nbFileFinder::create();
    $finder->prune(array('war', 'plugins', 'log', 'userContent'));

    if (!isset($options['workspace'])) {
      $this->log("excluding workspace\n", nbLogger::COMMENT);
      $finder->prune('workspace');
    }

    if (!isset($options['builds'])) {
      $this->log("excluding builds\n", nbLogger::COMMENT);
      $finder->prune('builds');
    }

    if (!isset($options['fingerprints'])) {
      $this->log("excluding fingerprints\n", nbLogger::COMMENT);
      $finder->prune('fingerprints');
    }

    $files = $finder->setType('file')->relative()->add('*.xml')->in($hudsonHome);

    if (isset($options['usercontent'])) {
      $this->log("Including userContent\n", nbLogger::COMMENT);
      $userContentFinder = new nbFileFinder();
      $userContent = $userContentFinder->relative()->add('*')->in($hudsonHome . '/userContent');
      foreach ($userContent as $file) {
        $files[] = 'userContent/' . $file;
      }
    }
    
//    if (isset($options['fingerprints'])) {
//      $fingerprintsFinder = new nbFileFinder();
//      $fingerptints = $fingerprintsFinder->relative()->add('*')->in($hudsonHome . '/fingerprints');
//      foreach ($fingerptints as $file) {
//        $files[] = 'fingerprints/' . $file;
//      }
//    }

//    print_r($files);
    foreach ($files as $file) {
      nbFileSystem::copy($hudsonHome . '/' . $file, $backupHome . '/' . $file);
    }

//    $zip = new ZipArchive();
//    $filename = 'hudson-backup-' . time() . '.zip';
//    if ($zip->open($filename, ZIPARCHIVE::CREATE) !== true)
//      throw new Exception('[nbBackupCommand:execute] cannot create zip file: ' . '');
//
//    $files = $finder->setType('file')->add('*')->in($backupHome);
//    foreach ($files as $file) {
//      $zip->addFile($file);
//      $zip->close();
//    }
    $time = strftime('%Y%m%d-%H%M%S', time());
    $this->log("$time - Backup succesful.", nbLogger::COMMENT);

    return true;
  }
}
