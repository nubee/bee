NameVirtualHost *:80

<VirtualHost *:80>
  ServerName <?php echo $serverName.'.'.$tld ?>

<?php if(count($alias)): ?>
  ServerAlias <?php echo implode(' ', $alias);?>
<?php endif; ?>

  DocumentRoot "<?php echo $projectPath ?>"
  DirectoryIndex %INDEX%

  <Directory "<?php echo $projectPath ?>">
    AllowOverride All
    Allow from All
  </Directory>

  Alias /sf "<?php echo $projectPath ?>/lib/vendor/symfony/data/web/sf"
  <Directory "<?php echo $projectPath ?>/lib/vendor/symfony/data/web/sf">
    AllowOverride All
    Allow from All
  </Directory>

</VirtualHost>
