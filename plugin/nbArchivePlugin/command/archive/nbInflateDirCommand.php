<?php

class nbInflateDirCommand extends nbCommand {

  protected function configure() {
    $this->setName('archive:inflate-dir')
            ->setBriefDescription('Inflates a directory in gzip format file')
            ->setDescription(<<<TXT
 The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('target-path', nbArgument::REQUIRED, 'Target path'),
                new nbArgument('target-dir', nbArgument::REQUIRED, 'Target directory'),
                new nbArgument('archive-path', nbArgument::REQUIRED, 'Archive path')
            )));

    $this->setOptions(new nbOptionSet(array(
                new nbOption('config-file', 'f', nbOption::PARAMETER_OPTIONAL, 'Archive configuration file', './.bee/nbArchivePlugin.yml')
            )));
  }
  protected function execute(array $arguments = array(), array $options = array()) {
    $timestamp = date('YmdHi', time());
    $targetPath = nbFileSystemUtils::sanitize_dir($arguments['target-path']);
    $targetDir = nbFileSystemUtils::sanitize_dir($arguments['target-dir']);
    $archivePath = nbFileSystemUtils::sanitize_dir($arguments['archive-path']);
    if (!file_exists($targetPath . '/' . $targetDir)) {
      throw new Exception("dir to inflate not found: " . $targetPath . '/' . $targetDir);
    }
    if (!file_exists($archivePath)) {
      throw new Exception("archive path not found");
    }
    $targetFile = $targetDir . '-' . $timestamp . '.tgz';
    $this->logLine('Archiving ' . $targetPath . '/' . $targetDir . ' in ' . $archivePath . '/' . $targetFile);
    $shell = new nbShell();
    $cmd = 'tar -czvf ' . $archivePath . '/' . $targetFile . ' -C ' . $targetPath . ' ' . $targetDir;
    $this->logLine('Tar command: ' . $cmd);
    $shell->execute($cmd);
    $this->logLine('Done- Inflate dir' . $targetDir);
    return true;
  }

}