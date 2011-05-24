<?php

class companyActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->companies = Doctrine_Core::getTable('Company')
                    ->createQuery('a')
                    ->execute();
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->company = CompanyTable::getInstance()->findOneBySlug($slug);
    $this->map = new GMap();
    $this->map->addMarker(new GMapMarker($this->company->getLatitude(),$this->company->getLongitude()));
    $this->map->centerAndZoomOnMarkers();
  }
}
