<?php use_helper('I18N') ?>
<?php echo __('Hi %first_name%', array('%first_name%' => $user->getFirstName()), 'sf_guard') ?>,<br/><br/>

<?php echo __('This e-mail is being sent because you requested information on how to reset your password.', null, 'sf_guard') ?><br/><br/>

<?php echo __('You can change your password by clicking the below link which is only valid for 24 hours:', null, 'sf_guard') ?><br/><br/>

<?php echo sfConfig::get('app_forgot_password_url').$forgot_password->unique_key ?>