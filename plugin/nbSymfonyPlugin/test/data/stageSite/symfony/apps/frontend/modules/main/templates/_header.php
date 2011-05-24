<div id="header-box">
  <div id="header" class="container_12">
    <?php echo link_to(image_tag('/images/logo.png', array('alt' => 'Findeal')), '@homepage') ?>
  </div>
</div>

<div id="nav-box">
  <div id="nav">
    <ul id="main-nav">
      <li class="separator"></li>
      <li><?php echo link_to('Last deals', '@homepage') ?></li>
      <li class="separator"></li>
      <li><?php echo link_to('Companies', '@companies') ?></li>
      <li class="separator"></li>
      <li><?php echo link_to('How it works', '@homepage') ?></li>
      <li class="separator"></li>
    </ul>
    <ul id="user-nav">
      <li class="separator"></li>
      <?php if($sf_user->isAuthenticated()) : ?>
        <li><?php echo link_to('Logout', '@logout') ?></li>
      <?php else : ?>
        <li><?php echo link_to('Register', '@sf_guard_register') ?></li>
        <li class="separator"></li>
        <li><?php echo link_to('Login', '@login') ?></li>
      <?php endif ?>
      <li class="separator"></li>
    </ul>
  </div>
</div>