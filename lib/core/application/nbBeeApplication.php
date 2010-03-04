<?php

/**
 * Represents an application that executes commands.
 *
 * @package    bee
 * @subpackage application
 */
class nbBeeApplication extends nbApplication
{
  protected function configure()
  {
    $this->name = 'bee';
    $this->version = '0.1.0';

    $this->loadCommands();
  }

  protected function formatSynopsys($synopsys)
  {
    $res = $this->format("Usage:", nbLogger::COMMENT) . "\n";
    $res .= ' ' . $this->format($synopsys, nbLogger::INFO) . "\n";

    return $res;
  }

  protected function formatArguments(nbArgumentSet $argumentSet, $max)
  {
    $arguments = $argumentSet->getArguments();
    if(count($arguments) == 0)
      return '';

    $res = "\n";
    $res .= $this->format("Arguments:", nbLogger::COMMENT) . "\n";

    foreach($arguments as $argument) {
      $res .= $this->format(sprintf(" %-{$max}s ", $argument->getName()), nbLogger::INFO);
      $res .= $argument->getDescription();
      if($argument->isRequired())
        $res .= $this->format(' (required)', nbLogger::COMMENT);
      else if(null !== $argument->getValue() && !$argument->isArray())
        $res .= $this->format(' (default: ' . $argument->getValue() . ')', nbLogger::COMMENT);
      $res .= "\n";
    }
    return $res;
  }

  protected function formatOptions(nbOptionSet $optionSet, $max)
  {
    $options = $optionSet->getOptions();
    if(count($options) == 0)
      return '';

    $res = "\n";
    $res .= $this->format("Options:", nbLogger::COMMENT) . "\n";
    foreach($options as $option) {
      $res .= $this->format(sprintf(" --%-{$max}s  ", $option->getName()), nbLogger::INFO);
      $res .= $this->format($option->hasShortcut() ? '-' . $option->getShortcut() : '  ', nbLogger::INFO);
      $res .= ' ' . $option->getDescription();
      if($option->hasOptionalParameter() && !$option->isArray())
        $res .= $this->format(' (default: ' . $option->getValue() . ')', nbLogger::COMMENT);
      $res .= "\n";
    }
    return $res;
  }

  protected function formatDescription($description, $indent = ' ')
  {
    if(!$description)
      return '';

    $res = "\n";
    $res .= $this->format('Description:', nbLogger::COMMENT) . "\n";

    $res .= ' ' . implode("\n ", explode("\n", $description)) . "\n";

    return $res;
  }
}
