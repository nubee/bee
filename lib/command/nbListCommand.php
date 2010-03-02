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
      ->setBriefDescription('print command list')
      ->setDescription(<<<TXT
The <info>list</info> command displays all available commands:
   ./bee list
TXT
        );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    // TODO: set list format (tabs?)
    $commandSet = $this->application->getCommands();
    $string = nbLogger::getInstance()->format('Available commands:', 'comment') . "\n";
    foreach ($commandSet->getCommands() as $command)
      $string .= nbLogger::getInstance()->format($command->getFullName(), 'info') . ' ' . $command->getBriefDescription() . "\n";
    nbLogger::getInstance()->log($string);
    return true;
  }
}
