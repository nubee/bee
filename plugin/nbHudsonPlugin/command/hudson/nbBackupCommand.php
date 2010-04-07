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
        new nbOption('hudson', 'h', nbOption::PARAMETER_NONE, 'Specify that also hudson server must be included'),
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
    $this->log("$time - Starting backup procedure...\n", nbLogger::COMMENT);

    $hudsonHome = $arguments['hudsonHome'];
    if (!is_dir($hudsonHome))
      throw new Exception('Cannot find hudson home directory: ' . $hudsonHome);

    $backupHome = $arguments['backupHome'];
    if (isset($options['autofolder']))
      $backupHome .= '/' . $time;
    $this->log('From ' . $hudsonHome . ' to ' . $backupHome . "\n", nbLogger::COMMENT);

    $finder = nbFileFinder::create();
    // hudson config
    $files = $finder->setType('file')->relative()->maxdepth(0)->add('*.xml')->in($hudsonHome);
    $files = array_merge($files, $this->findInSubFolder($hudsonHome, 'users', '*'));

    $excludeDirs = array();
    if (isset($options['builds']))
      $this->log("  + including job builds\n", nbLogger::COMMENT);
    else
      $excludeDirs[] = 'builds';
    if (isset($options['workspace']))
      $this->log("  + including job workspace\n", nbLogger::COMMENT);
    else
      $excludeDirs[] = 'workspace';
    $files = array_merge($files, $this->findInSubFolder($hudsonHome, 'jobs', '*', $excludeDirs));

    if (isset($options['fingerprints'])) {
      $this->log("  + including fingerprints\n", nbLogger::COMMENT);
      $files = array_merge($files, $this->findInSubFolder($hudsonHome, 'fingerprints', '*'));
    }

    if (isset($options['usercontent'])) {
      $this->log("  + including userContent\n", nbLogger::COMMENT);
      $files = array_merge($files, $this->findInSubFolder($hudsonHome, 'userContent', '*'));
    }

    if (isset($options['hudson'])) {
      $this->log("  + including hudson server\n", nbLogger::COMMENT);
      $files = array_merge($files, $this->findInSubFolder($hudsonHome, 'war', '*'));
      $files = array_merge($files, $this->findInSubFolder($hudsonHome, 'plugins', '*'));
    }

    // perform file copy
    nbFileSystem::mkdir($backupHome, true);
    $numFiles = count($files);
    $this->log("Copying $numFiles files...\n", nbLogger::COMMENT);
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
    $this->log("$time - Backup succesful.\n", nbLogger::COMMENT);

    return true;
  }

  private function findInSubFolder($hudsonHome, $dir, $pattern, $excludeDirs = array())
  {
    $finder = nbFileFinder::create();
    $files = $finder->relative()->prune($excludeDirs)->add($pattern)->in($hudsonHome . '/' . $dir);
    foreach ($files as $key => $file) {
      $files[$key] = $dir . '/' . $file;
    }
    return $files;
  }
}
