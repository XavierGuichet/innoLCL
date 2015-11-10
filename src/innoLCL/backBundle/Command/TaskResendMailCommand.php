<?php

/**
 * Description of TaskCommand
 *
 * @author pinacolada
 */
namespace innoLCL\backBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskResendMailCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('task:resendMailConfirm')
            ->setDescription('Task command that send again confirm Mail')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running Task command...</comment>');

        try {
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            
            //$users = $this->getDoctrine()->getManager()->getRepository('innoLCL\AllUserBundle\Entity\User')->getAllWithToken;
            /*
            $users = $this->getDoctrine()->getManager()->getRepository('innoLCL\AllUserBundle\Entity\User')->byId(7);
            if($users){
                foreach($users as $key =>$user){
                    $url = $this->generateUrl('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);

                    $message = \Swift_Message::newInstance()
                            ->setSubject('Registration confirmation')
                            ->setFrom('admin@acmedemo.com')
                            ->setTo($email)
                            ->setContentType('text/html')
                            ->setBody(
                            $this->renderView(
                                    ":email:confirmation.email.twig", array(
                                'user' => $user,
                                'confirmationUrl' => $url))
                            )
                    ;
                    $sent = $this->get('mailer')->send($message);
                }
            }
            */
            
        } catch (\Exception $e) {
            $output->writeln('<error>ERROR</error>'.$e->getMessage());
        }
        
        $output->writeln('<comment>Task done!</comment>');
    }
}
