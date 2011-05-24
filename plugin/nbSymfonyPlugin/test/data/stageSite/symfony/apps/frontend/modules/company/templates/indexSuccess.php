<?php box('Companies') ?>
<ul>
  <?php foreach ($companies as $company): ?>
  <li class="clearfix">
    <?php echo image_tag($company->getLogoUrl(), array('alt' => $company, 'width' => 120, 'height' => 80)) ?>
    <?php echo link_to($company, '@company?slug=' . $company->getSlug()) ?>
  </li>
  <?php endforeach; ?>
</ul>

<?php end_box() ?>