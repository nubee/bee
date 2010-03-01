<?php

/**
 *
 * Allow to build rules to find files and directories.
 *
 * All rules may be invoked several times, except for ->in() method.
 * Some rules are cumulative (->name() for example) whereas others are destructive
 * (most recent value is used, ->maxdepth() method for example).
 *
 * All methods return the current nbFileFinder object to allow easy chaining:
 *
 * $files = nbFileFinder::type('file')->name('*.php')->in(.);
 *
 * Interface loosely based on perl File::Find::Rule module.
 */
class nbFileFinder
{
  protected $type                   = 'file';
  protected $names                  = array();
  protected $prunes                 = array();
  protected $discards               = array();
  protected $execs                  = array();
  protected $mindepth               = 0;
  protected $sizes                  = array();
  protected $maxdepth               = 1000000;
  protected $relative               = false;
  protected $followLink            = false;
  protected $sort                   = false;
  protected $ignoreVersionControl = true;

  /**
   * Sets maximum directory depth.
   *
   * Finder will descend at most $level levels of directories below the starting point.
   *
   * @param  int $level
   * @return object current nbFileFinder object
   */
  public function maxdepth($level)
  {
    $this->maxdepth = $level;

    return $this;
  }

  /**
   * Sets minimum directory depth.
   *
   * Finder will start applying tests at level $level.
   *
   * @param  int $level
   * @return object current nbFileFinder object
   */
  public function setMinDepth($level)
  {
    $this->mindepth = $level;

    return $this;
  }

  public function getType()
  {
    return $this->type;
  }

  /**
   * Sets the type of elements to return.
   *
   * @param  string $name directory or file or any (for both file and directory)
   * @return object new nbFileFinder object
   */
  public static function create($name = 'file')
  {
    $finder = new self();
    return $finder->setType($name);
  }
  /**
   * Sets the type of elements to returns.
   *
   * @param  string $name  directory or file or any (for both file and directory)
   * @return nbFileFinder current object
   */
  public function setType($type)
  {
    $type = strtolower($type);

    if (substr($type, 0, 3) === 'dir')
      $this->type = 'directory';
    else if ($type === 'any')
      $this->type = 'any';
    else if ($type === 'file')
      $this->type = 'file';
    else
      throw new RangeException('[nbFileFinder::setType] Undefined type: ' . $type);

    return $this;
  }

  /**
   * Adds rules that files must match.
   *
   * You can use patterns (delimited with / sign), globs or simple strings.
   *
   * $finder->name('*.php')
   * $finder->name('/\.php$/') // same as above
   * $finder->name('test.php')
   *
   * @param  list a list of patterns, globs or strings
   * @return nbFileFinder current object
   */
  public function add($names)
  {
    $this->names = array_merge($this->names, $this->argsToArray($names));

    return $this;
  }

  /**
   * Adds rules that files must not match.
   *
   * @see    ->name()
   * @param  list a list of patterns, globs or strings
   * @return nbFileFinder current object
   */
  public function remove($names)
  {
    $this->names = array_merge($this->names, $this->argsToArray($names, false));

    return $this;
  }

  /**
   * Adds tests for file sizes.
   *
   * $finder->size('> 10K');
   * $finder->size('<= 1Ki');
   * $finder->size(4);
   *
   * @param  list   a list of comparison strings
   * @return nbFileFinder current object
   */
  public function size($sizes)
  {
    $count = count($sizes);
    for ($i = 0; $i < $count; ++$i)
      $this->sizes[] = new nbNumberCompare($sizes[$i]);

    return $this;
  }

  /**
   * Traverses no further.
   *
   * @param  list a list of patterns, globs to match
   * @return nbFileFinder current object
   */
  public function prune($prunes)
  {
    $this->prunes = array_merge($this->prunes, $this->argsToArray($prunes));

    return $this;
  }

  /**
   * Discards elements that matches.
   *
   * @param  list a list of patterns, globs to match
   * @return nbFileFinder current object
   */
  public function discard($discards)
  {
    $this->discards = array_merge($this->discards, $this->argsToArray($discards));

    return $this;
  }

  /**
   * Ignores version control directories.
   *
   * Currently supports Subversion, CVS, DARCS, Gnu Arch, Monotone, Bazaar-NG, GIT, Mercurial
   *
   * @param  bool $ignore false when version control directories shall be included (default is true)
   *
   * @return nbFileFinder current object
   */
  public function ignoreVersionControl($ignore = true)
  {
    $this->ignoreVersionControl = $ignore;

    return $this;
  }

  /**
   * Returns files and directories ordered by name
   *
   * @return nbFileFinder current object
   */
  public function sortByName()
  {
    $this->sort = 'name';

    return $this;
  }

  /**
   * Returns files and directories ordered by type (directories before files), then by name
   *
   * @return nbFileFinder current object
   */
  public function sortByType()
  {
    $this->sort = 'type';

    return $this;
  }

  /**
   * Executes function or method for each element.
   *
   * Element match if function or method returns true.
   *
   * $finder->exec('myfunction');
   * $finder->exec(array($object, 'mymethod'));
   *
   * @param  mixed  function or method to call
   * @return nbFileFinder current object
   */
  public function execute()
  {
    $args = func_get_args();
    $count = count($args);
    for ($i = 0; $i < $count; ++$i) {
      if (is_array($args[$i]) && !method_exists($args[$i][0], $args[$i][1]))
        throw new InvalidArgumentException(sprintf('[nbFileFinder::execute] Method "%s" does not exist for object "%s".', $args[$i][1], $args[$i][0]));

      if (!is_array($args[$i]) && !function_exists($args[$i]))
        throw new InvalidArgumentException(sprintf('[nbFileFinder::execute] Function "%s" does not exist.', $args[$i]));

      $this->execs[] = $args[$i];
    }

    return $this;
  }

  /**
   * Returns relative paths for all files and directories.
   *
   * @return nbFileFinder current object
   */
  public function relative()
  {
    $this->relative = true;

    return $this;
  }

  /**
   * Symlink following.
   *
   * @return nbFileFinder current object
   */
  public function followLink()
  {
    $this->followLink = true;

    return $this;
  }

  /**
   * Searches files and directories which match defined rules.
   *
   * @return array list of files and directories
   */
  public function in()
  {
    $files = array();
    $currentDir = getcwd();

    $finder = clone $this;

    if ($this->ignoreVersionControl) {
      $ignores = array('.svn', '_svn', 'CVS', '_darcs',
        '.arch-params', '.monotone', '.bzr', '.git', '.hg');

      $finder->discard($ignores)->prune($ignores);
    }

    // first argument is an array?
    $args = func_get_args();
    $count  = count($args);
    if ($count === 1 && is_array($args[0])) {
      $args = $args[0];
      $count  = count($args);
    }

    for ($i = 0; $i < $count; ++$i) {
      $dir = realpath($args[$i]);

      if (!is_dir($dir))
        continue;

      $dir = str_replace('\\', '/', $dir);

      // absolute path?
      if (!self::isPathAbsolute($dir))
        $dir = $currentDir.'/'.$dir;

      $new_files = str_replace('\\', '/', $finder->searchIn($dir));

      if ($this->relative)
        $new_files = str_replace(rtrim($dir, '/').'/', '', $new_files);

      $files = array_merge($files, $new_files);
    }

    if ($this->sort === 'name')
      sort($files);

    return array_unique($files);
  }

  protected function searchIn($dir, $depth = 0)
  {
    if ($depth > $this->maxdepth)
      return array();

    $dir = realpath($dir);

    if ((!$this->followLink) && is_link($dir))
      return array();

    $files = array();
    $temp_files = array();
    $temp_folders = array();
    if (is_dir($dir)) {
      $current_dir = opendir($dir);
      while (false !== $entryname = readdir($current_dir)) {
        if ($entryname == '.' || $entryname == '..') continue;

        $current_entry = $dir.DIRECTORY_SEPARATOR.$entryname;
        if ((!$this->followLink) && is_link($current_entry))
          continue;

        if (is_dir($current_entry)) {
          if ($this->sort === 'type')
            $temp_folders[$entryname] = $current_entry;
          else {
            if (($this->type === 'directory' || $this->type === 'any')
              && ($depth >= $this->mindepth)
              && !$this->isDiscarded($dir, $entryname)
              && $this->matchRules($dir, $entryname)
              && $this->hasExecuted($dir, $entryname))
              $files[] = $current_entry;

            if (!$this->isPruned($dir, $entryname))
              $files = array_merge($files, $this->searchIn($current_entry, $depth + 1));
          }
        }
        else {
          if (($this->type !== 'directory') 
            && ($depth >= $this->mindepth)
            && !$this->isDiscarded($dir, $entryname)
            && $this->matchRules($dir, $entryname)
            && $this->sizeOk($dir, $entryname)
            && $this->hasExecuted($dir, $entryname))
          {
            if ($this->sort === 'type')
              $temp_files[] = $current_entry;
            else
              $files[] = $current_entry;
          }
        }
      }

      if ($this->sort === 'type') {
        ksort($temp_folders);
        foreach($temp_folders as $entryname => $current_entry) {
          if (($this->type === 'directory' || $this->type === 'any') && ($depth >= $this->mindepth) && !$this->is_discarded($dir, $entryname) && $this->match_names($dir, $entryname) && $this->exec_ok($dir, $entryname))
            $files[] = $current_entry;

          if (!$this->isPruned($dir, $entryname))
            $files = array_merge($files, $this->search_in($current_entry, $depth + 1));
        }

        sort($temp_files);
        $files = array_merge($files, $temp_files);
      }

      closedir($current_dir);
    }

    return $files;
  }

  protected function matchRules($dir, $entry)
  {
    if (!count($this->names)) return true;

    $hasIncludeRule = false;
    $matched = false;
    $included = false;
    foreach ($this->names as $args) {
      list($in, $regex) = $args;
      //echo $entry . " ";
      //print_r($args);
      if($in)
        $hasIncludeRule = true;
      if (preg_match($regex, $entry)) {
        $matched = true;
        // We must match ONLY ONE "not_name" or "name" rule:
        // if "not_name" rule matched then we return "false"
        // if "name" rule matched then we return "true"
        //return $not ? false : true;
        $included = $in;
      }
    }

    $default = $hasIncludeRule ? false : true;
    return ($matched) ? $included : $default;
  }

  protected function sizeOk($dir, $entry)
  {
    if (0 === count($this->sizes)) return true;

    if (!is_file($dir.DIRECTORY_SEPARATOR.$entry)) return true;

    $filesize = filesize($dir.DIRECTORY_SEPARATOR.$entry);
    foreach ($this->sizes as $number_compare)
      if (!$number_compare->test($filesize)) return false;

    return true;
  }

  protected function isPruned($dir, $entry)
  {
    if (0 === count($this->prunes)) return false;

    foreach ($this->prunes as $args) {
      $regex = $args[1];
      if (preg_match($regex, $entry)) return true;
    }

    return false;
  }

  protected function isDiscarded($dir, $entry)
  {
    if (0 === count($this->discards)) return false;

    foreach ($this->discards as $args) {
      $regex = $args[1];
      if (preg_match($regex, $entry)) return true;
    }

    return false;
  }

  protected function hasExecuted($dir, $entry)
  {
    if (0 === count($this->execs)) return true;

    foreach ($this->execs as $exec)
      if (!call_user_func_array($exec, array($dir, $entry))) return false;

    return true;
  }

  public static function isPathAbsolute($path)
  {
    if ($path{0} === '/' || $path{0} === '\\' 
      || (strlen($path) > 3 && ctype_alpha($path{0})
        && $path{1} === ':' && ($path{2} === '\\' || $path{2} === '/')
      ))
    {
      return true;
    }

    return false;
  }

  /*
   * glob, patterns (must be //) or strings
   */
  protected function toRegex($str)
  {
    if (preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $str))
      return $str;

    return nbGlob::globToRegex($str);
  }

  protected function argsToArray($args, $include = true)
  {
    $list = array();
    if(!is_array($args))
      $list[] = array($include, $this->toRegex($args));
    else {
      $count = count($args);
      for ($i = 0; $i < $count; ++$i) {
        if (is_array($args[$i])) {
          foreach ($args[$i] as $arg)
            $list[] = array($include, $this->toRegex($arg));
        }
        else
          $list[] = array($include, $this->toRegex($args[$i]));
      }
    }

    return $list;
  }
}
