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
The <info>{$this->getFullName()}</info> displays the project configuration:

   <info>./bee {$this->getFullName()}</info>
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
    if(isset($options['filter'])) {
      $params = nbConfig::get($options['filter']);
      $params = nbArrayUtils::getAssociative($params);
      $message .= $this->formatLine('Filter is: <info>'.$options['filter'].'</info> ', nbLogger::COMMENT);
      $message .= "\n";
    }
    else
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
