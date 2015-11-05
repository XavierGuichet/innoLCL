<?php

namespace innoLCL\backBundle\Service;


class ServiceBack {
    protected $viewModerateur;
    protected $viewLecteur;
    protected $viewValidateur;
    protected $viewSelectionneur;
    
    public function __construct()
    {
    $this->viewModerateur = array("all");
    $this->viewLecteur = array("all","maybe","refused","validated");
    $this->viewValidateur = array("all","maybe","refused","validated");
    $this->viewSelectionneur = array("validated"); 
    }
    
    // Redirige les utilisateurs avec plus de 2 roles à l'authentification
    public function isUserWithTooManyRole($roles) {
        if(sizeof($roles) > 2) 
        { 
            return true;    
        }
        else 
        {
            return false;
        }
             
    }
    
    //retourne le role admin de l'user, supprime ROLE_USER
    public function getAdminRole($roles) {
        if(in_array("ROLE_MODERATEUR",$roles)) { return "ROLE_MODERATEUR";}
        if(in_array("ROLE_LECTEUR",$roles)) { return "ROLE_LECTEUR";}
        if(in_array("ROLE_VALIDATEUR",$roles)) { return "ROLE_VALIDATEUR";}
        if(in_array("ROLE_SELECTIONNEUR",$roles)) { return "ROLE_SELECTIONNEUR";}
    }
    
    public function canRoleViewThisList($viewstatuts,$userAdminRole) {
        if($userAdminRole == "ROLE_MODERATEUR" && in_array($viewstatuts,$this->viewModerateur)) { return true;}
        if($userAdminRole == "ROLE_LECTEUR") { return true;}
        if($userAdminRole == "ROLE_VALIDATEUR") { return true;}
        if($userAdminRole == "ROLE_SELECTIONNEUR" && in_array($viewstatuts,$this->viewSelectionneur)) { return true;}
        return false;
    }
    
    public function defaultViewOfRole($role) 
    {
        if($role == "ROLE_MODERATEUR") { return "all";}
        if($role == "ROLE_SELECTIONNEUR") { return "validated";}
    }
    
    public function canUserModeratedIdeaOnly($userAdminRole) {
        if($userAdminRole == "ROLE_VALIDATEUR") { return true;}
        return false;        
    }
    
     public function canUserValidatedIdeaOnly($userAdminRole) {
        if($userAdminRole == "ROLE_SELECTIONNEUR") { return true;}
        return false;
     }
     
     public function canUserEditThisIdea($role,$ideastatut,$ideavalidated) {
         if($role == "ROLE_MODERATEUR" && $ideastatut != "notmoderated") { return false;}
         if($role == "ROLE_LECTEUR") { return true;} //Peux voir toute les idées, il n'affiche pas de formulaire
         if($role == "ROLE_VALIDATEUR" && $ideastatut == "notmoderated") { return false;}
         if($role == "ROLE_SELECTIONNEUR" && $ideavalidated == false) { return false;}
         return true;
     }
}
