<?php
/**
 * @copyright  Copyright (c) 2009-2014 Steven TITREN - www.webaki.com
 * @package    Webaki\UserBundle\Redirection
 * @author     Steven Titren <contact@webaki.com>
 */

namespace innoLCL\AllUserBundle\Redirection;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class AfterLogoutRedirection implements LogoutSuccessHandlerInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * @param SecurityContextInterface $security
     */
    public function __construct(RouterInterface $router, SecurityContextInterface $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        $user = $this->security->getToken()->getUser();
        if($user){
            // Get list of roles for current user
            $roles = $this->security->getToken()->getUser()->getRoles();
            // Tranform this list in array
            $rolesTab = array_map(function($role){ 
                if(is_object($role)){
                    return $role->getRole(); 
                }else{
                    return null;
                }
            }, $roles);

            if (in_array('ROLE_MODERATEUR', $rolesTab, true) || in_array('ROLE_VALIDATEUR', $rolesTab, true) || in_array('ROLE_LECTEUR', $rolesTab, true) || in_array('ROLE_SELECTIONNEUR', $rolesTab, true))
                $response = new RedirectResponse($this->router->generate('fos_admin_user_security_login'));
            // otherwise we redirect user to the homepage of website
            else
                $response = new RedirectResponse($this->router->generate('innolcl_front_homepage'));
        }else{
            $response = new RedirectResponse($this->router->generate('innolcl_front_homepage'));
        }
        
        return $response;
    }
} 
