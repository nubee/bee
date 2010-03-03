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
    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('namespace', nbArgument::OPTIONAL, 'The namespace name')
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $commandList = array();
    $commandSet = $this->application->getCommands();

    foreach ($commandSet->getCommands() as $command)
      if ($arguments['namespace'] != '' && $command->getNamespace() == $arguments['namespace']
         || $arguments['namespace'] == '')
        $commandList[$command->getNamespace()][] = $command;

    $string = nbLogger::getInstance()->format('Available commands:', 'comment') . "\n";
    ksort($commandList);
    foreach ($commandList as $namespace => $commands) {
      if ($namespace != '')
        $string .= $this->format($namespace . ':', 'comment') . "\n";

      foreach ($commands as $command)                                                 // TODO: set list format (tabs?)
        $string .= '  ' . $this->format($command->getName(), 'info') . ' ' . $command->getBriefDescription() . "\n";
    }
    
    $this->log($string);
    return true;
  }
}
