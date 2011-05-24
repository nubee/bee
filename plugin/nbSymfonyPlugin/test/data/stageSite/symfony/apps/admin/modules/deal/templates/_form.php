<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('deal/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('deal/index') ?>">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'deal/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['company_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['company_id']->renderError() ?>
          <?php echo $form['company_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['deal_type_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['deal_type_id']->renderError() ?>
          <?php echo $form['deal_type_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['description']->renderLabel() ?></th>
        <td>
          <?php echo $form['description']->renderError() ?>
          <?php echo $form['description'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['price']->renderLabel() ?></th>
        <td>
          <?php echo $form['price']->renderError() ?>
          <?php echo $form['price'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['discount']->renderLabel() ?></th>
        <td>
          <?php echo $form['discount']->renderError() ?>
          <?php echo $form['discount'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['published_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['published_at']->renderError() ?>
          <?php echo $form['published_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['start_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['start_at']->renderError() ?>
          <?php echo $form['start_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['end_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['end_at']->renderError() ?>
          <?php echo $form['end_at'] ?>
        </td>
      </tr>      
    </tbody>
  </table>
</form>
