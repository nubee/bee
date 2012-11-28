<?php

class nbGitIntegrateCommand extends nbApplicationCommand
{

    protected function configure()
    {
        $this->setName('git:integrate')
            ->setBriefDescription('Commit work into the remote repository')
            ->setDescription(<<<TXT

  Show the working directory status:
  <info>./bee git:integrate</info>

  Add, commit, pull, test, push:
  <info>./bee git:integrate "A commit message" --force</info>

  Add, commit, pull, test, tag, push:
  <info>./bee git:integrate "A commit message" --tag=v1.0.0 --force</info>

TXT
        );

        $this->setArguments(new nbArgumentSet(array(
            new nbArgument('message', nbArgument::OPTIONAL, 'Commit message (write it between double quotes \'"\')'),
        )));

        $this->setOptions(new nbOptionSet(array(
            new nbOption('force', 'x', nbOption::PARAMETER_NONE, 'If not set, the working directory status will be shown'),
            new nbOption('tag', 't', nbOption::PARAMETER_REQUIRED, 'Create a tag and push it in remote server'),
        )));
    }

    protected function execute(array $arguments = array(), array $options = array())
    {
        $this->checkBeeProject();

        if (!nbConfig::has('project_repository_root-directory')) {
            throw new Exception('You must configure your project directory (absolute path!)');
        }

        $shell = new nbShell();

        if (!isset($options['force'])) {
            // git status
            $this->logLine("git status:", nbLogger::INFO);
            $command = 'git status';
            $shell->execute($command);

            // git tag
            $this->logLine("git tag", nbLogger::INFO);
            $command = 'git tag';
            $shell->execute($command);

            return 0;
        }

        if (!isset($arguments['message'])) {
            throw new Exception('You must write a commit message!');
        }

        $message = $arguments['message'];
        $repoDir = nbConfig::get('project_repository_root-directory');

        // git add <project repository directory>
        $command = sprintf('git add %s', $repoDir);
        $shell->execute($command);

        // git commit -m "<message>"
        $command = sprintf('git commit -m "%s"', $message);
        $shell->execute($command);

        // git pull
        $command = 'git pull';
        if (!$shell->execute($command)) {
            throw new Exception('There are conflicts. Resolve them and re-run this command.');
        }

        // test command
        if (!nbConfig::has('project_test-command')) {
            throw new Exception('You must configure your test command');
        }

        $command = nbConfig::get('project_test-command');
        if (!$shell->execute($command)) {
            throw new Exception('There are test failures. Fix them and re-run this command.');
        }

        if (isset($options['tag'])) {
            $tag = $options['tag'];
            $command = sprintf('git tag %s', $tag);
            $shell->execute($command);

            // git push with tags
            $command = 'git push origin master --tags';
        } else {
            // git push
            $command = 'git push';
        }

        $shell->execute($command);
    }

}