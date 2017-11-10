<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/proophessor-do-symfony for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/proophessor-do-symfony/blob/master/LICENSE.md New BSD License
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
use OldSound\RabbitMqBundle\DependencyInjection\Compiler\RegisterPartsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Console\Input\InputOption;
use Proto\Demo\Person;


class produceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('event:produce')
            ->setDescription('Create event_stream.')
            ->setHelp('This command creates the event_stream')
            ->addOption(
                'n',
                null,
                InputOption::VALUE_OPTIONAL,
                'How many times messages should be sent?',
                1
            );
            
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*$o = new Person();
        $o->setName("1234d\nsdsds\nqs");
        $o->setEmail("toto@titi.com");
        $o->setEmail("toto@titi.com2");
        $valser=$o->serializeToString();
        print sprintf("[%s] %d",$valser,strlen($valser));
        
        $o2 = new Person();
        $o2->mergeFromString($valser);
        var_dump($o2->getName());
        var_dump($o2->getEmail());*/
        
        $container = new ContainerBuilder();
        $container->registerExtension(new OldSoundRabbitMqExtension());
        $container->addCompilerPass(new RegisterPartsPass());
        $batchid=date('YmdHis');
        $n = $input->getOption('n');
        for($i=0;$i<$n;$i++){
            $msg = array('user_id' => $i,'batchid'=>$batchid);
            $this->getContainer()->get('old_sound_rabbit_mq.testEvent_producer')->publish(serialize($msg));
        }
        $output->writeln("<info>$n messages posted successfully.</info>");
    }
}
