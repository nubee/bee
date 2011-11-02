<?php

class nbChangeOwnershipCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('filesystem:change-ownership')
      ->setBriefDescription('Changes files ownership for a directory')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('dir', nbArgument::REQUIRED, 'Directory'),
        new nbArgument('user', nbArgument::REQUIRED, 'Owner user id'),
        new nbArgument('group', nbArgument::REQUIRED, 'Owner group')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $dir   = $arguments['dir'];
    $user  = $arguments['user'];
    $group = $arguments['group'];
    $doit  = isset($options['doit']);

    $this->logLine(sprintf('Changing ownership for project: %s to %s.%s', $dir, $user, $group));
    $cmd = sprintf('chown -R %s:%s %s', $user, $group, $dir);

    $this->executeShellCommand($cmd, $doit);
    $this->logLine('Ownership changed successfully!');
    
    return true;
  }

}