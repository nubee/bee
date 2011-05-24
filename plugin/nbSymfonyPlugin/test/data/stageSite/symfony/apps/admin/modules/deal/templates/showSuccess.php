<h1>Deal: </h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <td><?php echo $deal->getId() ?></td>
    </tr>
    <tr>
      <th>Company</th>
      <td><?php echo $deal->getCompany()->getName() ?></td>
    </tr>
    <tr>
      <th>Type</th>
      <td><?php echo $deal->getDealType()->getName() ?></td>
    </tr>
    <tr>
      <th>Name</th>
      <td><?php echo $deal->getName() ?></td>
    </tr>
    <tr>
      <th>Description</th>
      <td><?php echo $deal->getDescription() ?></td>
    </tr>
    <tr>
      <th>Price</th>
      <td><?php echo $deal->getPrice() ?></td>
    </tr>
    <tr>
      <th>Discount</th>
      <td><?php echo $deal->getDiscount() ?></td>
    </tr>
    <tr>
      <th>Published at</th>
      <td><?php echo $deal->getPublishedAt() ?></td>
    </tr>
    <tr>
      <th>Start at</th>
      <td><?php echo $deal->getStartAt() ?></td>
    </tr>
    <tr>
      <th>End at</th>
      <td><?php echo $deal->getEndAt() ?></td>
    </tr>
  </thead>
</table>
  <a href="<?php echo url_for('deal/index') ?>">Back to list</a>
  <a href="<?php echo url_for('deal/edit?id='.$deal->getId()) ?>">Edit</a>
