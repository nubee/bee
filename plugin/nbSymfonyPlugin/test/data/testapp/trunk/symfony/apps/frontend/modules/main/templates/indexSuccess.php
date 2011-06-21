<div class="box">
<h1><?php echo __("Enter your email") ?></h1>
  <form method="POST" action="<?php echo url_for('@create') ?>" id="user_form">
    <ul>
      <?php echo $form ?>
      <input type="submit" value="<?php echo __("Send") ?>" />
      <?php echo $form->renderHiddenFields(); ?>
      <?php echo $form->renderGlobalErrors(); ?>
    </ul>
  </form>
</div>