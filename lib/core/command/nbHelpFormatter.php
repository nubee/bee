<?php

/**
 * Represents n help formatter
 *
 * @package    bee
 * @subpackage command
 */
class nbHelpFormatter
{
  protected static function format($text, $level)
  {
    $logger = nbLogger::getInstance();
    return $logger->format($text, $level);
  }

  public static function formatSynopsys($synopsys)
  {
    $res = self::format("Usage:", nbLogger::COMMENT) . "\n";
    $res .= ' ' . self::format($synopsys, nbLogger::INFO) . "\n";

    return $res;
  }

  public static function formatArguments(nbArgumentSet $argumentSet, $max)
  {
    $arguments = $argumentSet->getArguments();
    if(count($arguments) == 0)
      return '';

    $res = "\n";
    $res .= self::format("Arguments:", nbLogger::COMMENT) . "\n";

    foreach($arguments as $argument) {
      $res .= self::format(sprintf(" %-{$max}s ", $argument->getName()), nbLogger::INFO);
      $res .= $argument->getDescription();
      if($argument->isRequired())
        $res .= self::format(' (required)', nbLogger::COMMENT);
      else if(null !== $argument->getValue() && !$argument->isArray())
        $res .= self::format(' (default: ' . $argument->getValue() . ')', nbLogger::COMMENT);
      $res .= "\n";
    }
    return $res;
  }

  public static function formatOptions(nbOptionSet $optionSet, $max)
  {
    $options = $optionSet->getOptions();
    if(count($options) == 0)
      return '';

    $res = "\n";
    $res .= self::format("Options:", nbLogger::COMMENT) . "\n";
    foreach($options as $option) {
      $res .= self::format(sprintf(" --%-{$max}s  ", $option->getName()), nbLogger::INFO);
      $res .= self::format($option->hasShortcut() ? '-' . $option->getShortcut() : '  ', nbLogger::INFO);
      $res .= ' ' . $option->getDescription();
      if($option->hasOptionalParameter() && !$option->isArray())
        $res .= self::format(' (default: ' . $option->getValue() . ')', nbLogger::COMMENT);
      $res .= "\n";
    }
    return $res;
  }

  public static function formatDescription($description, $indent = ' ')
  {
    if(!$description)
      return '';

    $res = "\n";
    $res .= self::format('Description:', nbLogger::COMMENT) . "\n";

    $res .= ' ' . implode("\n ", explode("\n", $description)) . "\n";

    return $res;
  }
}
