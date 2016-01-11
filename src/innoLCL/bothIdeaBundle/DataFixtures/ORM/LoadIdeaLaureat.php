<?php
// src/innoLCL/bothIdeaBundle/DataFixtures/ORM/LoadIdeaLaureat.php
namespace innoLCL\bothIdeaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use innoLCL\bothIdeaBundle\Entity\IdeaLaureat;

class LoadIdeaLaureat implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $ideas = array(
                  array("Jérémy","Monneau","Running'Epargne : faites de votre course une stratégie d'épargne et un acte solidaire","Direction Régionale Champs Elysées"),
                  array("Pascal","Malbo","Mensualités conso à la carte","Organisation Conseil Projets"),
                  array("Nicolas","Ségard","Manager 2.0 : La communication locale, le Manager LCL devient le Social Media Manager de son agence","Direction Régionale Lyon Centre Est"),
                  array("Bernard","Gerstein","Friendbox, le nouveau service client LCL de cagnotte électronique","Direction information et technologies et animation des processus"),
                  array("Arnaud","Girerd","La It List du conseiller",""),
                  array("Marie-Hélène","Cazanave","ID2C : Identification Digitale du Client et du Collaborateur","Direction Régionale Gironde"),
                  array("Patrick","Rivière","Easycash : Dites oui aux achats coup de cœur","Direction information et technologies et animation des processus"),
                  array("Sylvian","Durix","L’effet WOW LCL : Comment enchanter nos clients ?","Direction Régionale Gironde"),
                  array("Éric","Sommervogel","ZEF (Zéro Facturettes), la solution ultime pour en finir avec vos facturettes","Direction du management de la donnée et de la relation client"),
                  array("Zahia","Bouhadji","Alerte départ à l’étranger","Direction de la Banque Commerciale")
    );

    foreach ($ideas as $idea) {
  		$ObjIdea = new IdeaLaureat();
      $ObjIdea->setTitre($idea[2]);
      $ObjIdea->setNomAuthor($idea[1]);
      $ObjIdea->setPrenomAuthor($idea[0]);
      $ObjIdea->setDirectionAuthor($idea[3]);
  		$manager->persist($ObjIdea);
  	}

    $manager->flush();
  }
}
