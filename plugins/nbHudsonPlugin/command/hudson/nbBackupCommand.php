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
        new nbArgument('hudsonHome', nbArgument::REQUIRED, 'The hudson HOME directory')
      )))
      ->setOptions(new nbOptionSet(array(
        new nbOption('workspace', 'w', nbOption::PARAMETER_NONE, 'Specify that workspace must be included'),
        new nbOption('fingerprints', 'f', nbOption::PARAMETER_NONE, 'Specify that fingerprints must be included'),
        new nbOption('builds', 'b', nbOption::PARAMETER_NONE, 'Specify that build history must be included'),
        new nbOption('usercontent', 'u', nbOption::PARAMETER_NONE, 'Specify that user content must be included'),
        new nbOption('hudson', 'h', nbOption::PARAMETER_NONE, 'Specify that also hudson server must be included'),
        new nbOption('copy', 'c', nbOption::PARAMETER_OPTIONAL, 'Create an uncompressed copy to a forder (folder name can be specified as value)', 'hudson-backup'),
        new nbOption('zip', 'z', nbOption::PARAMETER_OPTIONAL, 'Create a zip file (file name can be specified as value)', 'hudson-backup'),
        new nbOption('autoname', 'a', nbOption::PARAMETER_NONE, 'Generate automatically subfolders and files name')
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
    $this->log("[$time] - Starting backup procedure...\n", nbLogger::COMMENT);

    $hudsonHome = $arguments['hudsonHome'];
    if (!is_dir($hudsonHome))
      throw new Exception('Cannot find hudson home directory: ' . $hudsonHome);

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

    $numFiles = count($files);
    // perform file copy
    if (isset($options['copy'])) {
      $backupHome = $options['copy'];
      if (isset($options['autoname']))
        $backupHome .= '-' . $time;
      nbFileSystem::mkdir($backupHome, true);
      $progress = new nbProgress($numFiles, 8);
      $this->log("Copying from $hudsonHome to $backupHome $numFiles files... ", nbLogger::COMMENT);
      foreach ($files as $key => $file) {
        if (($p = $progress->getProgress($key)) !== null)
          $this->log("$p% - ");
        nbFileSystem::copy($hudsonHome . '/' . $file, $backupHome . '/' . $file);
      }
      $this->log("100%\n");
    }

    // create zip file
    if (isset($options['zip'])) {
      if (class_exists('ZipArchive')) {
        $filename = $options['zip'];
        if (isset($options['autoname']))
          $filename .= '-' . $time;
        $filename .= '.zip';
        $this->log("Creating ZIP file $filename ... ", nbLogger::COMMENT);
        $zip = new ZipArchive();
        if ($zip->open($filename, ZIPARCHIVE::CREATE) !== true)
          throw new Exception('[nbBackupCommand:execute] cannot create zip file: ' . $filename);

        $progress = new nbProgress($numFiles, 8);
        foreach ($files as $key => $file) {
          if (($p = $progress->getProgress($key)) !== null)
            $this->log("$p% - ");
          $zip->addFile($hudsonHome . '/' . $file, $file);
        }
        $zip->close();
        $this->log("100%\n");
      }
      else
        $this->log("Zip support not found\n");
    }

    $time = strftime('%Y%m%d-%H%M%S', time());
    $this->log("[$time] - Backup succesful.\n", nbLogger::COMMENT);

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
