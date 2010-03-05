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
        new nbOption('redirect', '', nbOption::PARAMETER_REQUIRED, 'Redirects output to file')
      )))
      ->setBriefDescription('Executes a shell command')
      ->setDescription(<<<TXT
The <info>shell:execute</info> executes a shell command:

   <info>./bee shell:execute command</info>
TXT
        );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Executing: ' . $arguments['command_name'], nbLogger::COMMENT);
    $this->log("\n\n");

    $shell = new nbShell(isset($options['redirect']));
    $shell->execute($arguments['command_name']);
  }
}