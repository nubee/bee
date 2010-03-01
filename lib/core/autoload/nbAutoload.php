<?php

include_once dirname(__FILE__) . '/../system/nbFileFinder.php';
include_once dirname(__FILE__) . '/../util/nbGlob.php';

class nbAutoload
{
  static protected
    $registered = false,
    $instance   = null;

  protected
    $files        = array(),
    $classes      = array(),
    $overriden    = array();

  protected function __construct() { }

  /**
   * Retrieves the singleton instance of this class.
   *
   *
   * @return Autoload   A sfSimpleAutoload implementation instance.
   */
  static public function getInstance()
  {
    if (!isset(self::$instance))
      self::$instance = new nbAutoload();

    return self::$instance;
  }

  /**
   * Register sfSimpleAutoload in spl autoloader.
   *
   * @return void
   */
  static public function register()
  {
    if (self::$registered)
      return;

    ini_set('unserialize_callback_func', 'spl_autoload_call');
    if (false === spl_autoload_register(array(self::getInstance(), 'autoload')))
      throw new sfException(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));

    self::$registered = true;
  }

  /**
   * Unregister sfSimpleAutoload from spl autoloader.
   *
   * @return void
   */
  static public function unregister()
  {
    spl_autoload_unregister(array(self::getInstance(), 'autoload'));
    self::$registered = false;
  }

  /**
   * Handles autoloading of classes.
   *
   * @param  string $class A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function autoload($class)
  {
    $class = strtolower($class);

    // class already exists
    if (class_exists($class, false) || interface_exists($class, false))
      return true;

    // we have a class path, let's include it
    if (isset($this->classes[$class])) {
        require $this->classes[$class];

      return true;
    }

    return false;
  }

  /**
   * Adds a directory to the autoloading system if not yet present and give it the highest possible precedence.
   *
   * @param string $dir The directory to look for classes
   * @param string $ext The extension to look for
   */
  public function addDirectory($dir, $ext = '.php', $recursive = false)
  {
    $finder = nbFileFinder::create('file');
    if(!$recursive)
        $finder->maxdepth(0);

    $finder->followLink()->add('*' . $ext);
    $this->addFiles($finder->in($dir), false);
  }

  /**
   * Adds files to the autoloading system.
   *
   * @param array   $files    An array of files
   * @param Boolean $register Whether to register those files as single entities (used when reloading)
   */
  public function addFiles(array $files, $register = true)
  {
    foreach ($files as $file)
      $this->addFile($file, $register);
  }

  /**
   * Adds a file to the autoloading system.
   *
   * @param string  $file     A file path
   * @param Boolean $register Whether to register those files as single entities (used when reloading)
   */
  public function addFile($file, $register = true)
  {
    if (!is_file($file))
      return;

    if (in_array($file, $this->files))
      return;

    if ($register)
      $this->files[] = $file;

    preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+)~mi', file_get_contents($file), $classes);
    foreach ($classes[1] as $class)
      $this->classes[strtolower($class)] = $file;
  }

  /**
   * Sets the path for a particular class.
   *
   * @param string $class A PHP class name
   * @param string $path  An absolute path
   */
  public function setClassPath($class, $path)
  {
    $class = strtolower($class);

    $this->overriden[$class] = $path;

    $this->classes[$class] = $path;
  }

  /**
   * Returns the path where a particular class can be found.
   *
   * @param string $class A PHP class name
   *
   * @return string|null An absolute path
   */
  public function getClassPath($class)
  {
    $class = strtolower($class);

    return isset($this->classes[$class]) ? $this->classes[$class] : null;
  }
}
