<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Findeal Admin Interface</title>
    <?php use_stylesheet('admin.css') ?>
    <?php include_javascripts() ?>
    <?php include_stylesheets() ?>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <h1>
          <a href="<?php echo url_for('homepage') ?>">
            <img src="/images/Findeal.png" alt="Findeal- admin area"  width="200" height="100" />
          </a>
        </h1>
      </div>
<?php if ($sf_user->hasCredential('admin_auth')): ?>

      <div id="menu">
        <ul>
          <li><?php echo link_to('Categories', 'category') ?></li>
          <li><?php echo link_to('Companies', 'company') ?></li>
          <li><?php echo link_to('Deal Types', 'deal_type') ?></li>
          <li><?php echo link_to('Deals', 'deal') ?></li>
          <li><?php echo link_to('Users', 'sf_guard_user') ?></li>
          <li><?php echo link_to('Groups', 'sf_guard_group') ?></li>
          <li><?php echo link_to('Permissions', 'sf_guard_permission') ?></li>
          <li><?php echo link_to('Logout', 'sf_guard_signout') ?></li>
        </ul>
      </div>
<?php endif ?>

      <div id="content">
        <?php echo $sf_content ?>
      </div>

      <div id="footer">
        <img src="/images/Findeal.png" width="100" height="50"/>
        powered by <a href="http://di-side.meandmymind.com/">
        <img src="/images/di-side.jpg" alt="Di-Side"  width="150" height="50"/></a>
      </div>
    </div>
  </body>
</html>