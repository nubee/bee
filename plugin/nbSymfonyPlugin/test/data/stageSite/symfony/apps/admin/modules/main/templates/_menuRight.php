<div class="box">
  <h1>Action</h1>
      <div class="box-inner">
        <ul>
          <li><?php echo link_to('Show profile', 'profile') ?></li>
          <li><?php echo link_to('Add Deal', 'deal/new') ?></li>
          <li><?php echo link_to('Add Company', 'company/new') ?></li>
        </ul>
      </div>
</div>
<?php include_component('deal', 'expired') ?>
