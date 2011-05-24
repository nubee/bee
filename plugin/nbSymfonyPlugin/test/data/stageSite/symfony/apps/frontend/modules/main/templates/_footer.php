<div id="footer-box">
  <div id="footer" class="container_12 clearfix">
    <div class="grid_4">
      <h2><?php echo __('About us') ?></h2>
      <ul>
        <li><?php echo link_to('The company', '@homepage') ?></li>
        <li><?php echo link_to('Contacts', '@homepage') ?></li>
        <li><?php echo link_to('Terms and conditions', '@homepage') ?></li>
        <li><?php echo link_to('Privacy', '@homepage') ?></li>
      </ul>
    </div>
    <div class="grid_4">
      <h2><?php echo __('Learn more') ?></h2>
      <ul>
        <li><?php echo link_to('FAQ', '@homepage') ?></li>
        <li><?php echo link_to('How it works', '@homepage') ?></li>
      </ul>
    </div>
    <div class="grid_4">
      <h2><?php echo __('Last tweets') ?></h2>
      <ul>
        <li><?php echo link_to('Last tweets', '@homepage') ?></li>
      </ul>
    </div>

    <div class="grid_12 copyright">
      &copy; <?php echo date('Y') ?> <?php echo link_to('DI-SIDE', 'http://www.di-side-com') ?>. All rights reserved.
    </div>
  </div>
</div>