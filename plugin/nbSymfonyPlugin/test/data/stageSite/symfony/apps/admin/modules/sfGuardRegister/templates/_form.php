<?php use_helper('I18N') ?>

<form action="<?php echo url_for('@sf_guard_register_create') ?>" method="post">
  <table>
    <?php echo $form ?>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" name="register" value="<?php echo __('Submit', null, 'sf_guard') ?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>