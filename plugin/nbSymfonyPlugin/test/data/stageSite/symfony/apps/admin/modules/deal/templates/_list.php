<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Company</th>
      <th>Type</th>
      <th>Name</th>
      <th>Price</th>
      <th>Discount</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($deals as $deal): ?>
    <tr>
      <td><?php echo $deal->getId() ?></td>
      <td><?php echo $deal->getCompany()->getName() ?></td>
      <td><?php echo $deal->getDealType()->getName() ?></td>
      <td><?php echo $deal->getName() ?></td>
      <td><?php echo $deal->getPrice() ?></td>
      <td><?php echo $deal->getDiscount() ?></td>
      <td>
        <a href="<?php echo url_for('deal/edit?id='.$deal->getId()) ?>">Edit</a>
        <a href="<?php echo url_for('deal/show?id='.$deal->getId()) ?>">Show</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
