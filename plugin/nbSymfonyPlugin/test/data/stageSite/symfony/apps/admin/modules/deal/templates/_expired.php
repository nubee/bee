<div>
  <h1>Expired deals</h1>
  <ul>
  <?php foreach($deals as $deal): ?>
    <li>
      <?php echo $deal->getEndAt() ?>
      <?php echo link_to($deal->getName(),'deal/show?id='.$deal->getId()) ?>
    </li>
  <?php endforeach ?>
  </ul>
</div>
