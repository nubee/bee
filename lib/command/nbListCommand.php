<?php

/**
 * Prints command list.
 *
 * @package    bee
 * @subpackage command
 */
class nbListCommand extends nbApplicationCommand
{
  protected function configure()
  {
    $this->setName('list')
      ->setBriefDescription('List commands')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command displays all available commands:

   <info>./bee {$this->getFullName()}</info>
TXT
        );
    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('namespace', nbArgument::OPTIONAL, 'The namespace name')
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $list = array();
    $commandSet = $this->getApplication()->getCommands();

    $namespace = $arguments['namespace'];
    $showSingleNamespace = $namespace != '';

    $max = 0;
    foreach ($commandSet->getCommands() as $command) {
      if (!$showSingleNamespace || $command->getNamespace() == $namespace)
        $list[$command->getNamespace()][] = $command;

      if($max < strlen($command->getName()))
        $max = strlen($command->getName());
    }

    if($showSingleNamespace)
      $res = $this->formatLine(sprintf('Available commands in namespace "%s":', $namespace), nbLogger::COMMENT);
    else
      $res = $this->formatLine('Available commands:', nbLogger::COMMENT);

    ksort($list);
    $lastNamespace = '';
    foreach ($list as $ns => $commands) {
      if ($ns != $lastNamespace && !$showSingleNamespace)
        $res .= $this->format($ns . ':', nbLogger::COMMENT) . "\n";

      foreach ($commands as $command) {
        $res .= '  ' . $this->format(sprintf("%-{$max}s", $command->getName()), nbLogger::INFO);
        $res .= '  ' . $command->getBriefDescription() . "\n";
      }
    }
    
    $this->log($res);
    return true;
  }
}
