<h1>Profile</h1>
<table>
  <thead>
    <tr>
      <th>First name</th>
      <td><?php echo $user->getGuardUser()->getFirstName() ?></td>
    </tr>
    <tr>
      <th>Last name</th>
      <td><?php echo $user->getGuardUser()->getLastName() ?></td>
    </tr>
    <tr>
      <th>email</th>
      <td><?php echo $user->getGuardUser()->getEmailAddress() ?></td>
    </tr>
    <tr>
      <th>Birthday</th>
      <td><?php echo $user->getProfile()->getBirthday() ?></td>
    </tr>
    <tr>
      <th>Address</th>
      <td><?php echo $user->getProfile()->getAddress() ?></td>
    </tr>
    <tr>
      <th>Zip code</th>
      <td><?php echo $user->getProfile()->getZipCode() ?></td>
    </tr>
    <tr>
      <th>Country</th>
      <td><?php echo $user->getProfile()->getCountry() ?></td>
    </tr>
    <tr>
      <th>Nationality</th>
      <td><?php echo $user->getProfile()->getNationality() ?></td>
    </tr>
    <tr>
      <th>Phone number</th>
      <td><?php echo $user->getProfile()->getPhoneNumber() ?></td>
    </tr>
    <tr>
      <th>Fax number</th>
      <td><?php echo $user->getProfile()->getFaxNumber() ?></td>
    </tr>
    <tr>
      <th>Website</th>
      <td><?php echo $user->getProfile()->getWebsite() ?></td>
    </tr>
  </thead>
</table>
<a href="<?php echo url_for('profile/edit') ?>">Edit</a>