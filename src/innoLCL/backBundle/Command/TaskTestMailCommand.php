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

class TaskTestMailCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('task:testMail')
            ->setDescription('Task command that send a test Mail')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running Task command...</comment>');

        try {
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            
            $output->writeln('<comment>Mail : idée nouvelle</comment>');
            $result = $this->getContainer()->get('mail_to_user')->sendEmailIdeeFront("derjuju@yopmail.com", "newIdea");                        
            if($result) $output->writeln('<info>SUCCESS</info>');
            else $output->writeln('<error>ERROR!</error>');
            
            $output->writeln('<comment>Mail : idée modifiée</comment>');
            $result = $this->getContainer()->get('mail_to_user')->sendEmailIdeeFront("derjuju@yopmail.com", "modifyIdea");                        
            if($result) $output->writeln('<info>SUCCESS</info>');
            else $output->writeln('<error>ERROR!</error>');
            
            $output->writeln('<comment>Mail : idée améliorée</comment>');
            $result = $this->getContainer()->get('mail_to_user')->sendEmailIdeeFront("derjuju@yopmail.com", "improveIdea");                        
            if($result) $output->writeln('<info>SUCCESS</info>');
            else $output->writeln('<error>ERROR!</error>');
            
            $output->writeln('<comment>Mail : idée validée</comment>');
            $result = $this->getContainer()->get('mail_to_user')->sendEmailValider("derjuju@yopmail.com");                        
            if($result) $output->writeln('<info>SUCCESS</info>');
            else $output->writeln('<error>ERROR!</error>');
            
            $output->writeln('<comment>Mail : idée à améliorer</comment>');
            $result = $this->getContainer()->get('mail_to_user')->sendEmailPeutEtre("derjuju@yopmail.com");                        
            if($result) $output->writeln('<info>SUCCESS</info>');
            else $output->writeln('<error>ERROR!</error>');
            
            $output->writeln('<comment>Mail : idée refusée</comment>');
            $result = $this->getContainer()->get('mail_to_user')->sendEmailRefuser("derjuju@yopmail.com", "Votre idée est trop similaire à une autre ou non applicable dans le contexte de LCL.");                        
            if($result) $output->writeln('<info>SUCCESS</info>');
            else $output->writeln('<error>ERROR!</error>');
            
            
            
        } catch (\Exception $e) {
            $output->writeln('<error>ERROR</error>'.$e->getMessage());
        }
        
        $output->writeln('<comment>Task done!</comment>');
    }
}
