<?php

/**
 * Displays project configuration.
 *
 * @package    bee
 * @subpackage command
 */
class nbShellExecuteCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('shell:execute')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('command_name', nbArgument::REQUIRED, 'The command to execute')
      )))
      ->setOptions(new nbOptionSet(array(
        new nbOption('redirect', 'r', nbOption::PARAMETER_NONE, 'Redirects output to file')
      )))
      ->setBriefDescription('Executes a shell command')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> executes a shell command or an alias defined in project.yml:

   <info>./bee {$this->getFullName()} command</info>
TXT
        );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    if(! $arg = $this->getAlias($arguments['command_name']))
      $arg = $arguments['command_name'];

    $this->log('Executing: ' . $arg, nbLogger::COMMENT);
    $this->log("\n\n");
    $shell = new nbShell(isset($options['redirect']));
    $shell->execute($arg);
  }
  
  protected function getAlias($command)
  {
    $aliases = nbConfig::get('proj_shell_aliases');
    if(null !== $aliases && is_array($aliases) && array_key_exists($command, $aliases)) {
      $this->log('Alias found for ' . $command, nbLogger::COMMENT);
      $this->log("\n\n");
      return $aliases[$command];
    }
    else
      return null;
  }
  
}
