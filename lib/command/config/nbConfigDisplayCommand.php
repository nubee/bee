<?php

/**
 * Displays project configuration.
 *
 * @package    bee
 * @subpackage command
 */
class nbConfigDisplayCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('config:display')
      ->setBriefDescription('Displays project configuration')
      ->setDescription(<<<TXT
The <info>config:display</info> displays the project configuration:

   <info>./bee config:display</info>
TXT
        );

    $this->setOptions(new nbOptionSet(array(
      new nbOption('filter', 'f', nbOption::PARAMETER_REQUIRED, 'Print only a subset of keys'),
    )));

  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $message = $this->formatLine('Project configuration', nbLogger::COMMENT);
    $message .= "\n";
    $params = nbConfig::getAll(true);
    ksort($params);

    $max = 0;
    foreach($params as $param => $value) {
      if($max < strlen($param))
        $max = strlen($param);
    }

    foreach($params as $param => $value) {
      $message .= $this->format(sprintf(" %-{$max}s", $param), nbLogger::INFO);
      $message .= sprintf("  => %s\n", $this->format($value, nbLogger::COMMENT));
    }

    $this->log($message);
  }
}
