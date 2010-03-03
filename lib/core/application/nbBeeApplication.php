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
  }

  protected function formatSynopsys($synopsys)
  {
    $res = $this->format("Usage:", 'comment') . "\n";
    $res .= ' ' . $this->format($synopsys, 'info') . "\n";

    return $res;
  }

  protected function formatArguments(nbArgumentSet $argumentSet, $max)
  {
    $arguments = $argumentSet->getArguments();
    if(count($arguments) == 0)
      return '';

    $res = "\n";
    $res .= $this->format("Arguments:", 'comment') . "\n";

    foreach($arguments as $argument) {
      $res .= $this->format(sprintf(" %-{$max}s ", $argument->getName()), 'info');
      $res .= $argument->getDescription();
      if($argument->isRequired())
        $res .= $this->format(' (required)', 'comment');
      else if(null !== $argument->getValue() && !$argument->isArray())
        $res .= $this->format(' (default: ' . $argument->getValue() . ')', 'comment');
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
    $res .= $this->format("Options:", 'comment') . "\n";
    foreach($options as $option) {
      $res .= $this->format(sprintf(" %-{$max}s %s", $option->getName(), $option->getShortcut()), 'info');
      $res .= ' ' . $option->getDescription();
      if($option->hasOptionalParameter() && !$option->isArray())
        $res .= $this->format(' (default: ' . $option->getValue() . ')', 'comment');
      $res .= "\n";
    }
    return $res;
  }

  protected function formatDescription($description, $indent = ' ')
  {
    if(!$description)
      return '';

    $res = "\n";
    $res .= $this->format('Description:', 'comment') . "\n";

    $res .= ' ' . implode("\n ", explode("\n", $description)) . "\n";

    return $res;
  }
}
