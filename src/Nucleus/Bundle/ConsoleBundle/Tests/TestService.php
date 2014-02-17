<?php

namespace Nucleus\Bundle\ConsoleBundle\Tests;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of TestableCommandLineService
 */
class TestService
{
    /**
     * Comment from the method
     *
     * @\Nucleus\Console\CommandLine
     *
     * @param string $name The name of the person you want to say hello to
     * @param OutputInterface $output
     * @param InputInterface $output
     * @param Command $parentCommand
     */
    public function hello($name, OutputInterface $output, InputInterface $input, Command $parentCommand)
    {
        $output->write('Hello ' . $name . ' !');
    }
}