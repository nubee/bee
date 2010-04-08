<?php

/**
 * Progress handler.
 *
 * @package    bee
 * @subpackage util
 */

class nbProgress
{
  private $progress = array();

  public function __construct($maxValue, $numSteps) {
    for ($i = 0; $i <= $numSteps; ++$i) {
      $this->progress[round($maxValue/$numSteps * $i)] = round($i/$numSteps * 100, 1);
    }
  }

  public function getProgress($value) {
    return isset($this->progress[$value]) ? $this->progress[$value] : null;
  }
}
