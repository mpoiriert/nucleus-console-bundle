<?php

namespace Nucleus\Bundle\ConsoleBundle;

use Nucleus\Bundle\ConsoleBundle\ServiceCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Nucleus\Invoker\IInvoker;

class NucleusConsoleBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        parent::registerCommands($application);

        if(!method_exists($application,'getKernel')) {
            return;
        }

        $container = $application->getKernel()->getContainer();
        $commands = $container->getParameter('nucleus.console.commands');

        if(!$commands) {
            $commands = array();
        }

        foreach($commands as $commandParameter) {
            $command = new ServiceCommand(
                $commandParameter['name'],
                $commandParameter['shortDesc'],
                $commandParameter['paramsArray'],
                $commandParameter['serviceName'],
                $commandParameter['methodName'],
                $container->get(IInvoker::NUCLEUS_SERVICE_NAME),
                $container
            );
            $application->add($command);
        }
    }
}