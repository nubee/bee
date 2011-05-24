<?php box(__('Deal of the day')) ?>
  <img src="/uploads/pictures/pic1.jpg" alt="Deal 1" />
  <h2>Quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut</h2>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec porttitor
    fermentum scelerisque. Nulla libero velit, faucibus quis euismod ut,
    dignissim viverra nibh. Nunc rhoncus ornare enim, nec pretium sem feugiat
    nec. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec
    porttitor fermentum scelerisque.
  </p>

  <?php echo link_to('More info...', '@homepage', array('class' => 'more')) ?>
  <div class="details">
    <?php echo link_to(image_tag('/images/btn_buy_now.png'), '@homepage', array('class' => 'button')) ?>
    <table>
      <tr>
        <th>Value</th>
        <th>Discount</th>
        <th>Save</th>
        <th>Time Left</th>
        <th>Sold</th>
      </tr>
      <tr>
        <td>50€</td>
        <td>50%</td>
        <td>25%</td>
        <td>12:05:23</td>
        <td>14</td>
      </tr>
    </table>
  </div>
<?php end_box() ?>
