<?php

/**
 * Match globbing patterns against text.
 *
 *   if match_glob("foo.*", "foo.bar") echo "matched\n";
 *
 * // prints foo.bar and foo.baz
 * $regex = glob_to_regex("foo.*");
 * for (array('foo.bar', 'foo.baz', 'foo', 'bar') as $t)
 * {
 *   if (/$regex/) echo "matched: $car\n";
 * }
 *
 * sfGlobToRegex implements glob(3) style matching that can be used to match
 * against text, rather than fetching names from a filesystem.
 *
 * based on perl Text::Glob module.
 *
 * @package    bee
 * @subpackage system
 */
class nbGlob
{
  protected static $strictLeadingDot = true;
  protected static $strictWildcardSlash = true;

  public static function setStrictLeadingDot($boolean)
  {
    self::$strictLeadingDot = $boolean;
  }

  public static function setStrictWildcardSlash($boolean)
  {
    self::$strictWildcardSlash = $boolean;
  }

  /**
   * Returns a compiled regex which is the equiavlent of the globbing pattern.
   *
   * @param  string $glob  pattern
   * @return string regex
   */
  public static function globToRegex($glob)
  {
    $first_byte = true;
    $escaping = false;
    $in_curlies = 0;
    $regex = '';
    $size = strlen($glob);
    for ($i = 0; $i < $size; $i++) {
      $car = $glob[$i];
      if ($first_byte) {
        if (self::$strictLeadingDot && $car !== '.')
          $regex .= '(?=[^\.])';

        $first_byte = false;
      }

      if ($car === '/')
        $first_byte = true;

      if ($car === '.' || $car === '(' || $car === ')' || $car === '|' || $car === '+' || $car === '^' || $car === '$')
        $regex .= "\\$car";
      elseif ($car === '*')
        $regex .= ($escaping ? '\\*' : (self::$strictWildcardSlash ? '[^/]*' : '.*'));
      elseif ($car === '?')
        $regex .= ($escaping ? '\\?' : (self::$strictWildcardSlash ? '[^/]' : '.'));
      elseif ($car === '{') {
        $regex .= ($escaping ? '\\{' : '(');
        if (!$escaping) ++$in_curlies;
      }
      elseif ($car === '}' && $in_curlies) {
        $regex .= ($escaping ? '}' : ')');
        if (!$escaping) --$in_curlies;
      }
      elseif ($car === ',' && $in_curlies)
        $regex .= ($escaping ? ',' : '|');
      elseif ($car === '\\') {
        if ($escaping) {
          $regex .= '\\\\';
          $escaping = false;
        }
        else
          $escaping = true;

        continue;
      }
      else 
        $regex .= $car;

      $escaping = false;
    }

    return '#^'.$regex.'$#';
  }
}
