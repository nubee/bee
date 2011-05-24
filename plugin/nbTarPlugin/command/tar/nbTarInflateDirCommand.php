<?php

class nbTarInflateDirCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('tar:inflate-dir')
      ->setBriefDescription('')
      ->setDescription(<<<TXT
 The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('target_path', nbArgument::REQUIRED, 'Target path'),
      new nbArgument('archive_path', nbArgument::REQUIRED, 'Archive path'),
      new nbArgument('target_dir', nbArgument::REQUIRED, 'Target directory')
    )));

    $this->setOptions(new nbOptionSet(array(
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
      $timestamp = date('YmdHi',  time());
      $target_file = $arguments['target_dir'].'-'.$timestamp.'.tgz';
      $this->logLine('Tar '.$arguments['target_path'].'/'.$arguments['target_dir'].' in '.$arguments['archive_path'].'/'.$target_file);
      $shell = new nbShell();
      $cmd = 'tar -czvf '.$arguments['archive_path'].'/'.$target_file.' -C '.$arguments['target_path'].' '.$arguments['target_dir'];
      $this->logLine('Tar command: '.$cmd);
      $shell->execute($cmd);
      $this->logLine('Done- Inflate dir'.$arguments['target_dir']);
      return true;

  }

}