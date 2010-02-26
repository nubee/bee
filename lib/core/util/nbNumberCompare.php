<?php

/**
 * Numeric comparisons.
 *
 * sfNumberCompare compiles a simple comparison to an anonymous
 * subroutine, which you can call with a value to be tested again.

 * Now this would be very pointless, if sfNumberCompare didn't understand
 * magnitudes.

 * The target value may use magnitudes of kilobytes (k, ki),
 * megabytes (m, mi), or gigabytes (g, gi).  Those suffixed
 * with an i use the appropriate 2**n version in accordance with the
 * IEC standard: http://physics.nist.gov/cuu/Units/binary.html
 *
 * based on perl Number::Compare module.
 *
 * @package    bee
 * @subpackage util
 */
class nbNumberCompare
{
  protected $test = '';

  public function __construct($test)
  {
    $this->test = $test;
  }

  public function test($number)
  {
    if (!preg_match('{^([<>]=?)?(.*?)([kmg]i?)?$}i', $this->test, $matches))
      throw new sfException(sprintf('don\'t understand "%s" as a test.', $this->test));

    $target = array_key_exists(2, $matches) ? $matches[2] : '';
    $magnitude = array_key_exists(3, $matches) ? $matches[3] : '';
    if (strtolower($magnitude) === 'k')  $target *=           1000;
    if (strtolower($magnitude) === 'ki') $target *=           1024;
    if (strtolower($magnitude) === 'm')  $target *=        1000000;
    if (strtolower($magnitude) === 'mi') $target *=      1024*1024;
    if (strtolower($magnitude) === 'g')  $target *=     1000000000;
    if (strtolower($magnitude) === 'gi') $target *= 1024*1024*1024;

    $comparison = array_key_exists(1, $matches) ? $matches[1] : '==';
    if ($comparison === '==' || $comparison == '')
      return ($number == $target);

    if ($comparison === '>')
      return ($number > $target);

    if ($comparison === '>=')
      return ($number >= $target);

    if ($comparison === '<')
      return ($number < $target);

    if ($comparison === '<=')
      return ($number <= $target);

    return false;
  }
}
