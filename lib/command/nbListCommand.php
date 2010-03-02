<?php

/**
 * Prints command list.
 *
 * @package    bee
 * @subpackage command
 */
class nbListCommand extends nbCommand
{
  private $application = null;

  public function  __construct(nbApplication $application)
  {
    parent::__construct();
    $this->application = $application;
  }

  protected function configure()
  {
    $this->setName('list')
      ->setBriefDescription('List commands')
      ->setDescription(<<<TXT
The <info>list</info> command displays all available commands:

   <info>./bee list</info>
TXT
        );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    // TODO: set list format (tabs?)
    $commandSet = $this->application->getCommands();
    $res = $this->formatLine('Available commands:', 'comment');

    foreach ($commandSet->getCommands() as $command)
      $res .= $this->format($command->getFullName(), 'info') . ' ' . $command->getBriefDescription() . "\n";

    $this->log($res);
    return true;
  }
}
