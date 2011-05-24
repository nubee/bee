<?php use_helper('GMap') ?>
<?php use_stylesheet('map') ?>

<?php box($company) ?>

<h2><?php echo __('Contacts') ?></h2>
<div>
<ul>
  <li><?php echo $company->getCity() ?></li>
  <li><?php echo $company->getAddress() ?></li>
  <li><?php echo $company->getPhoneNumber() ?></li>
  <li>
    <?php echo image_tag($company->getLogoUrl(), array('alt' => $company, 'width' => 240, 'height' => 160)) ?>
  </li>
</ul>
</div>
<div id="map">
  <?php include_map($map); ?>
</div>
<?php end_box() ?>

<div class="clearfix"></div>

<?php if($company->hasDeals()) : ?>
  <?php box('Recent deals') ?>
  <ul>
    <?php foreach($company->getDeals() as $deal) : ?>
    <li><?php echo link_to($deal, 'deal/show?id=' . $deal->getId()) ?></li>
    <?php endforeach ?>
  </ul>
  <?php end_box() ?>
<?php endif ?>

<?php include_map_javascript($map); ?>
