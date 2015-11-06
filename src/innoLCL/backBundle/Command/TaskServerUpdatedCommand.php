<?php

/**
 * Description of CronJobsCommand
 *
 * @author pinacolada
 */
namespace innoLCL\backBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class TaskServerUpdatedCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('task:notificationServerUpdate')
            ->setDescription('Tâche alertant la bonne mise à jour du serveur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running Task...</comment>');
        
        //sudo -Hu www-data git fetch && git log ..origin/master --pretty=format:"%h - %an, %ar : %s" > app/logs/history-git.log
        
        $message = "Le serveur a été mis à jour depuis github.<hr>";
        
        if(file_exists(realpath('app/logs/history-git.log'))){
            $logFile = new \SplFileObject(realpath('app/logs/history-git.log'));
            if (($handle = fopen($logFile->getRealPath(), "r")) !== FALSE) {            
                while (($row = fgets($handle)) !== FALSE) {
                      $message.=$row."<br>";          
                }
                fclose($handle);
            }

            $mail = \Swift_Message::newInstance()
                    ->setSubject('[LCL] server updated - '.$this->getContainer()->getParameter('server_name'))
                    ->setFrom("julien@freetouch.fr", "Julien")
                    ->setBody($message)
                    ->setReplyTo("julien@freetouch.fr", "Julien")
                    ->setContentType('text/html');

            try {
                $mail->setTo("julien@freetouch.fr");
                $mail->setCc("erwan@freetouch.fr");
                $mail->setCc("anaelle@freetouch.fr");
                $this->getContainer()->get('mailer')->send($mail);
                return true;
            } catch (\Swift_TransportException $exc) {
                return false;
            }
        }
        
        $output->writeln('<comment>Task done!</comment>');
    }
}
