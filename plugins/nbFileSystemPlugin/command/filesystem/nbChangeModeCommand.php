<?php

class nbChangeModeCommand extends nbCommand {

  protected function configure() {
    $this->setName('filesystem:change-mode')
      ->setBriefDescription('Changes files mode for a directory')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('dir', nbArgument::REQUIRED, 'Directory'),
        new nbArgument('mode', nbArgument::REQUIRED, 'Mode')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $dir = $arguments['dir'];
    $mode = $arguments['mode'];

    $this->logLine(sprintf('Changing mode for directory %s in %s', $dir, $mode));
    $cmd = sprintf('chmod -R %s %s', $mode, $dir);

    $this->executeShellCommand($cmd);
    $this->logLine('Mode changed successfully!');

    return true;
  }

}