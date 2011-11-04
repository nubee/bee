<?php

/**
 * Represents a command to execute.
 *
 * @package    bee
 * @subpackage argument
 */
abstract class nbCommand
{
  private
  $name,
  $namespace,
  $briefDescription = '',
  $description = '',
  $argumentSet = null,
  $optionSet = null,
  $aliases = array();
  
  private $parser;
  private $logger;
  private $verbose;

  public function __construct()
  {
    $this->argumentSet = new nbArgumentSet();
    $this->optionSet = new nbOptionSet();

    $this->logger = nbLogger::getInstance();
    $this->configure();
    
    if(!$this->getName())
      throw new InvalidArgumentException('Command name must be set');

    // Config file option must be added after setName
    if(!$this->optionSet->hasOption('config-file')) {
      $this->optionSet->addOption(
        new nbOption('config-file', '', nbOption::PARAMETER_OPTIONAL, 'Reads configuration from file', $this->generateDefaultConfigFile())
      );
    }
  }
  
  public function generateDefaultConfigFile() {
    $configFile = nbString::uncamelize($this->getNamespace());
    if($configFile != '')
      $configFile .= '-';
    
    $configFile .= nbString::uncamelize($this->getName());
    
    return $configFile . '.yml';
  }
  
  public function getTemplateConfigFilename() {
    $templateConfigFilename = nbString::uncamelize($this->getNamespace());
    if($templateConfigFilename != '')
      $templateConfigFilename .= '-';
    
    $templateConfigFilename .= nbString::uncamelize($this->getName());
    
    return $templateConfigFilename . '.template.yml';
  }
  
  public function run(nbCommandLineParser $parser, $commandLine, $verbose = false)
  {
    $this->parser = $parser;
    $this->parser->addArguments($this->getArguments());
    $this->parser->addOptions($this->getOptions());
    
    $this->parser->parse($commandLine, $this->getNamespace(), $this->getName());
    
    if (!$this->parser->hasOptionValue('config-file')) // command::execute must check arguments and options!
      if(!$this->parser->isValid())
        throw new InvalidArgumentException(sprintf(
            "[nbCommand::run] Command \"%s\" execution failed: \n  - %s", $this->getFullName(), implode("  \n- ", $this->parser->getErrors())
        ));
    
    $this->verbose = $verbose;

    return $this->execute($this->parser->getArgumentValues(), $this->parser->getOptionValues());
  }

  protected abstract function configure();

  protected abstract function execute(array $arguments = array(), array $options = array());

  public function setName($name)
  {
    $pos = strpos($name, ':');
    if(false !== $pos) {
      $namespace = substr($name, 0, $pos);
      $name = substr($name, $pos + 1);
    }
    else
      $namespace = '';

    if($name === null || strlen($name) == 0)
      throw new InvalidArgumentException('[nbCommand::setName] Name can\'t be empty');

    $this->namespace = $namespace;
    $this->name = $name;

    return $this;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getNamespace()
  {
    return $this->namespace;
  }

  public function getFullName()
  {
    $namespace = ($this->namespace != null ? $this->namespace . ':' : '');
    return $namespace . $this->name;
  }

  public function setArguments(nbArgumentSet $arguments)
  {
    $this->argumentSet = $arguments;
    return $this;
  }

  public function addArgument(nbArgument $argument)
  {
    $this->argumentSet->addArgument($argument);
    return $this;
  }

  public function getArguments()
  {
    return $this->argumentSet;
  }

  public function getArgumentsArray()
  {
    return $this->argumentSet->getArguments();
  }

  public function setOptions(nbOptionSet $options)
  {
    $this->optionSet = $options;
    return $this;
  }

  public function addOption(nbOption $option)
  {
    $this->optionSet->addOption($option);
    return $this;
  }

  public function getOptions()
  {
    return $this->optionSet;
  }

  public function getOptionsArray()
  {
    return $this->optionSet->getOptions();
  }

  public function hasShortcut($shortcut)
  {
    $pos = strpos($shortcut, ':');
    if(false !== $pos) {
      $namespace = substr($shortcut, 0, $pos);
      $name = substr($shortcut, $pos + 1);
    }
    else {
      $namespace = '';
      $name = $shortcut;
    }

    if(substr($this->namespace, 0, strlen($namespace)) != $namespace)
      return false;
    if(substr($this->name, 0, strlen($name)) != $name)
      return false;

    return true;
  }

  /*
   * Returns true if the command has aliases
   */

  public function hasAliases()
  {
    return count($this->aliases) > 0;
  }

  /*
   * Returns true if the command has alias with given name
   */

  public function hasAlias($alias)
  {
    return isset($this->aliases[$alias]);
  }

  /*
   * Sets an alias
   */

  public function setAlias($alias)
  {
    if($this->hasAlias($alias))
      throw new InvalidArgumentException(sprintf('[nbCommand::setAlias] Alias %s already defined.', $alias));

    $this->aliases[$alias] = $alias;
    return $this;
  }

  /*
   * Sets an array of aliases
   */

  public function setAliases(array $aliases)
  {
    foreach($aliases as $alias)
      $this->setAlias($alias);

    return $this;
  }

  public function setBriefDescription($description)
  {
    $this->briefDescription = $description;
    return $this;
  }

  public function getBriefDescription()
  {
    return $this->briefDescription;
  }

  public function setDescription($description)
  {
    $this->description = $description;
    return $this;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function log($text, $level = null)
  {
//    if($this->verbose)
      $this->logger->log($text, $level);
  }

  public function logLine($text, $level = null)
  {
//    if($this->verbose)
      $this->logger->logLine($text, $level);
  }

  public function format($text, $level)
  {
    return $this->logger->format($text, $level);
  }

  public function formatLine($text, $level)
  {
    return $this->logger->formatLine($text, $level);
  }

  public function getSynopsys()
  {
    return $this->getName() . $this->getArguments() . $this->getOptions();
  }

  public function getFileSystem()
  {
    return nbFileSystem::getInstance();
  }
  
  public function getParser() {
    return $this->parser;
  }
  
  public function getLogger()
  {
    return $this->logger;
  }
  
  public function isVerbose()
  {
    return $this->verbose;
  }
  
  public function executeShellCommand($command, $doit = true, $successCode = 1) {
    $shell = new nbShell();
    $code = $shell->execute($command, $doit);
    if($code != $successCode)
      throw new Exception(sprintf('Command "%s" exited with error: %s', $command, $code));
    
    return $shell->getOutput();
  }
  
  public function checkConfiguration($configDir, $configFilename) {
    $configFile = $this->parser->checkDefaultConfigurationDirs($configFilename);

    if(!file_exists($configFile)) 
      throw new InvalidArgumentException(sprintf('Config file "%s" does not exist', $configFilename));

    // Check configuration
    $checker = new nbConfigurationChecker(array(
      'logger' => $this->getLogger(),
      'verbose' => $this->isVerbose()
    ));
    
    $configuration = new nbConfiguration();
    $configuration->add(nbConfig::getAll());
    $configuration->add(sfYaml::load($configFile), '', true);

    try {
      $checker->check($configDir . '/' . $this->getTemplateConfigFilename(), $configuration);
    }
    catch (Exception $e) {
      $this->logLine('Configuration file doesn\'t match the template', nbLogger::ERROR);

      $printer = new nbConfigurationPrinter();
      $printer->addConfiguration($configuration->getAll());
//      $printer->addConfigurationFile($configFile);
      $printer->addConfigurationErrors($checker->getErrors());

      $this->logLine($printer->printAll());

      throw $e;
    }
    
    return $configFile;
  }
  
  public function loadConfiguration($configDir, $configFilename) {
    $configFile = $this->checkConfiguration($configDir, $configFilename);
    
    $yamlParser = new nbYamlConfigParser();
    $yamlParser->parseFile($configFile, '', true);
  }

}
