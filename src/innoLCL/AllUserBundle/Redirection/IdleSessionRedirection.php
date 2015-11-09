<?php
// src/AppBundle/EventListener/ExceptionListener.php
namespace innoLCL\AllUserBundle\Redirection;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class IdleSessionRedirection
{

    protected $session;
    protected $securityContext;
    protected $router;
    protected $maxIdleTime;

    public function __construct(SessionInterface $session, SecurityContextInterface $securityContext, RouterInterface $router, $maxIdleTime = 0)
    {
        $this->session = $session;
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->maxIdleTime = $maxIdleTime;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()) {

            return;
        }
        if (!$this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return;
        }
        
        if ($this->maxIdleTime > 0) {

            $this->session->start();
            $lapse = time() - $this->session->getMetadataBag()->getLastUsed();

            if ($lapse > $this->maxIdleTime) {
                dump("You have been logged out due to inactivity.");
                $roles = $this->securityContext->getToken()->getRoles();
                // Tranform this list in array
                $rolesTab = array_map(function($role){ 
                    return $role->getRole(); 
                }, $roles);

                if (in_array('ROLE_MODERATEUR', $rolesTab, true) || in_array('ROLE_VALIDATEUR', $rolesTab, true) || in_array('ROLE_LECTEUR', $rolesTab, true) || in_array('ROLE_SELECTIONNEUR', $rolesTab, true))
                    $response = new RedirectResponse($this->router->generate('fos_admin_user_security_login'));
                else
                    $response = new RedirectResponse($this->router->generate('innolcl_front_homepage'));

               
                $this->securityContext->setToken(null);
                $this->session->getFlashBag()->set('info', 'Vous êtes déconnecté car vous êtes inactif depuis plus de 15 minutes.');
                
                $event->setResponse($response);

            }
        }
    }

}
