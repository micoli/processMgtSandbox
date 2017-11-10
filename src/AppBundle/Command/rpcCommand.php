<?php

namespace AppBundle\Command;

use OldSound\RabbitMqBundle\DependencyInjection\Compiler\RegisterPartsPass;
use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class rpcCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('event:producerpc')
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

        $container = new ContainerBuilder();
        $container->registerExtension(new OldSoundRabbitMqExtension());
        $container->addCompilerPass(new RegisterPartsPass());

        $n = $input->getOption('n');

        $client = $this->getContainer()->get('old_sound_rabbit_mq.integer_store_rpc');
        /** @var RpcClient $client */
        for ($i = 0; $i < 10; $i++) {
            print ".";
            $client->addRequest(serialize(array('min' => 0, 'max' => 10)), 'rpc', 'id' . $i, '', 5);
        }
        try {
            $replies = $client->getReplies();
        } catch (AMQPTimeoutException $e) {
            print "timeout ";
        }
        print_r($replies);
        $output->writeln("<info>$n messages posted successfully.</info>");
    }
}