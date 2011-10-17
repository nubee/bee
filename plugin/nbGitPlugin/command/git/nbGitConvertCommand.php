<?php

/**
 * Shows local repository status.
 *
 * @package    bee
 * @subpackage command
 */
class nbGitConvertCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('git:convert')
      ->setBriefDescription('Convert an SVN repository to git')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command converts a subversion repository to a git one:

    <info>./bee {$this->getFullName()}</info>
TXT
      )
    ;
     
    $this->addArgument(new nbArgument('project', nbArgument::REQUIRED, 'The project name'));
    $this->setOptions(new nbOptionSet(array(
      new nbOption('source', 's', nbOption::PARAMETER_REQUIRED, 'Source repository'),
      new nbOption('destination', 'd', nbOption::PARAMETER_REQUIRED, 'Destination repository'),
      new nbOption('username', 'u', nbOption::PARAMETER_REQUIRED, 'Destination repository username'),
      new nbOption('new-name', 'n', nbOption::PARAMETER_REQUIRED, 'Destination name'),
      new nbOption('trunk', '', nbOption::PARAMETER_REQUIRED, 'Trunk folder'),
      new nbOption('tags', '', nbOption::PARAMETER_REQUIRED, 'Tags folder'),
      new nbOption('branches', '', nbOption::PARAMETER_REQUIRED, 'Branches folder'),
      new nbOption('no-standard-layout', 'x', nbOption::PARAMETER_NONE, 'No standard layout'),
      new nbOption('temp-dir', '', nbOption::PARAMETER_REQUIRED, 'Temporary dir'),
      new nbOption('authors-file', 'a', nbOption::PARAMETER_REQUIRED, 'Authors file'),
      new nbOption('dry-run', '', nbOption::PARAMETER_NONE, 'Dry run'),
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $project = $arguments['project'];

    if (!$options['source'])
      throw new Exception('Undefined source');
    
    if (!$options['destination'])
      throw new Exception('Undefined destination');
    
    if (!$options['username'])
      throw new Exception('Undefined username');
    
    $destName = isset($options['new-name']) ? $options['new-name'] : $project;
    $source = $options['source'] . '/' . $project;
    $destination = $options['username'] . '@' . $options['destination'] . '/' . $destName;
    $authorsFile = isset($options['authors-file']) ? $options['authors-file'] : false;
    $tempDir = isset($options['temp-dir']) ? $options['temp-dir'] : '~/temp';
    
    $this->logLine('Converting ' . $project, nbLogger::COMMENT);
    $this->logLine('Source: ' . $source, nbLogger::COMMENT);
    $this->logLine('Destination: ' . $destination, nbLogger::COMMENT);
    if($authorsFile)
      $this->logLine('Authors: ' . $destination, nbLogger::COMMENT);
    
    $trunk = (isset($options['trunk'])) ? $options['trunk'] : false;
    $tags = (isset($options['tags'])) ? $options['tags'] : false;
    $branches = (isset($options['branches'])) ? $options['branches'] : false;
    
    $useStandardLayout = (!$trunk && !$tags && !$branches && !isset($options['no-standard-layout']));
    $this->logLine('Standard layout: ' . ($useStandardLayout ? 'true' : 'false'), nbLogger::COMMENT);

    $this->logLine('Removing temporary directory: ' . $tempDir, nbLogger::INFO);
    nbFileSystem::rmdir($tempDir, true);
    
    $shell = new nbShell();
    $command = sprintf('git svn clone %s %s', $source, $tempDir);
    
    $params = array(' --no-metadata');
    
    if($authorsFile) $params[] = '-A' . $authorsFile;
    if($trunk) $params[] = '-T' . $trunk;
    if($tags) $params[] = '-t' . $tags;
    if($branches) $params[] = '-b' . $branches;
    if($useStandardLayout) $params[] = '--stdlayout';
    
    $dryRun = isset($options['dry-run']);
    
    $this->logLine('Cloning repository', nbLogger::INFO);
    
    $shell->execute($command . implode(' ', $params), $dryRun);
    
    $shell->execute('cd ' . $tempDir . ' && git svn-abandon-fix-refs', $dryRun);
    $shell->execute('cd ' . $tempDir . ' && git svn-abandon-cleanup', $dryRun);
    $shell->execute('cd ' . $tempDir . ' && git config --remove-section svn', $dryRun);
    $shell->execute('cd ' . $tempDir . ' && git config --remove-section svn-remote.svn', $dryRun);

    if(!isset($options['dry-run'])) {
      nbFileSystem::rmdir($tempDir . '/.git/svn', true);
      nbFileSystem::rmdir($tempDir . '/.git/refs/remotes/svn', true);
      nbFileSystem::rmdir($tempDir . '/.git/logs/refs/remotes/svn', true);
    }
    
    $shell->execute('cd ' . $tempDir . sprintf(' && git remote add origin %s.git', $destination), $dryRun);
    $shell->execute('cd ' . $tempDir . ' && git push --all', $dryRun);
    $shell->execute('cd ' . $tempDir . ' && git push --tags', $dryRun);
  }
}
