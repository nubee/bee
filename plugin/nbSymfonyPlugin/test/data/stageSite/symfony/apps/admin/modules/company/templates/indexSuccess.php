<h1>Companies List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Company</th>
      <th>City</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($companies as $company): ?>
    <tr>
      <td><?php echo $company->getId() ?></td>
      <td><?php echo $company->getName() ?></td>
      <td><?php echo $company->getCity() ?></td>
      <td>
        <a href="<?php echo url_for('company/edit?id='.$company->getId()) ?>">Edit</a>
        <a href="<?php echo url_for('company/show?id='.$company->getId()) ?>">Show</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>