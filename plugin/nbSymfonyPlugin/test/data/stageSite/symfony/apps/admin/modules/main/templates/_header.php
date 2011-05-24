<div id="header-box">
  <div id="header" class="container_12">
    <?php echo link_to(image_tag('/images/logo.png', array('alt' => 'Findeal')), '@homepage') ?>
  </div>
</div>

<?php if($sf_user->isAuthenticated()) : ?>
<div id="nav-box">
  <div id="nav">
    <ul id="main-nav">
      <li class="separator"></li>
      <li><?php echo link_to('Companies', '@company') ?></li>
      <li class="separator"></li>
      <li><?php echo link_to('Deals', '@deal') ?></li>
      <li class="separator"></li>
      <li><?php echo link_to('Statistics', '@homepage') ?></li>
      <li class="separator"></li>
    </ul>
    <ul id="user-nav">
      <li class="separator"></li>
      <li><?php echo link_to('Logout', '@logout') ?></li>
      <li class="separator"></li>
    </ul>
  </div>
</div>
<?php endif ?>
