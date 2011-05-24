<h1>Company: </h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <td><?php echo $company->getId() ?></td>
    </tr>
    <tr>
      <th>Name</th>
      <td><?php echo $company->getName() ?></td>
    </tr>
    <tr>
      <th>Description</th>
      <td><?php echo $company->getDescription() ?></td>
    </tr>
    <tr>
      <th>Partita iva</th>
      <td><?php echo $company->getPartitaIva() ?></td>
    </tr>
    <tr>
      <th>City</th>
      <td><?php echo $company->getCity() ?></td>
    </tr>
    <tr>
      <th>Addess</th>
      <td><?php echo $company->getAddress() ?></td>
    </tr>
    <tr>
      <th>State</th>
      <td><?php echo $company->getState() ?></td>
    </tr>
    <tr>
      <th>Zip code</th>
      <td><?php echo $company->getZipcode() ?></td>
    </tr>
    <tr>
      <th>Phone number</th>
      <td><?php echo $company->getPhoneNumber() ?></td>
    </tr>
    <tr>
      <th>Mobile number</th>
      <td><?php echo $company->getMobileNumber() ?></td>
    </tr>
    <tr>
      <th>Fax number</th>
      <td><?php echo $company->getFaxNumber() ?></td>
    </tr>
    <tr>
      <th>Email</th>
      <td><?php echo $company->getEmail() ?></td>
    </tr>
    <tr>
      <th>Logo</th>
      <td><?php echo $company->getLogo() ?></td>
    </tr>
    <tr>
      <th>Web site</th>
      <td><?php echo $company->getWebsite() ?></td>
    </tr>
    <tr>
      <th>Slug</th>
      <td><?php echo $company->getSlug() ?></td>
    </tr>
    <tr>
      <th>Latitude</th>
      <td><?php echo $company->getLatitude() ?></td>
    </tr>
    <tr>
      <th>Longitude</th>
      <td><?php echo $company->getLongitude() ?></td>
    </tr>
  </thead>
</table>
  <a href="<?php echo url_for('company/index') ?>">Back to list</a>
  <a href="<?php echo url_for('company/edit?id='.$company->getId()) ?>">Edit</a>
