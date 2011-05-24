<h1>Deals List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Company</th>
      <th>Name</th>
      <th>Description</th>
      <th>Price</th>
      <th>Discount</th>
      <th>Published at</th>
      <th>Start at</th>
      <th>End at</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($deals as $deal): ?>
    <tr>
      <td><a href="<?php echo url_for('deal/edit?id='.$deal->getId()) ?>"><?php echo $deal->getId() ?></a></td>
      <td><?php echo $deal->getCompanyId() ?></td>
      <td><?php echo $deal->getName() ?></td>
      <td><?php echo $deal->getDescription() ?></td>
      <td><?php echo $deal->getPrice() ?></td>
      <td><?php echo $deal->getDiscount() ?></td>
      <td><?php echo $deal->getPublishedAt() ?></td>
      <td><?php echo $deal->getStartAt() ?></td>
      <td><?php echo $deal->getEndAt() ?></td>
      <td><?php echo $deal->getCreatedAt() ?></td>
      <td><?php echo $deal->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('deal/new') ?>">New</a>
