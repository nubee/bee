<?php

class Company extends BaseCompany
{
  public function hasDeals()
  {
    return $this->getDeals()->count() > 0;
  }

  public function hasLogo()
  {
    $filename = sfConfig::get('sf_web_dir') . $this->getLogoUrl();
    return file_exists($filename) && is_file($filename);
  }

  public function getLogoUrl()
  {
    return '/uploads/logos/' . $this->getLogo();
  }

}