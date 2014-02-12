<?php

namespace Nucleus\Bundle\ConsoleBundle\Tests;

use Symfony\Component\Console\Output\Output;

/**
 * Description of TestableCommandLineService
 *
 * @author AxelBarbier
 */
class TestService {
    /**
     * Comment from the function 
     * @\Nucleus\Console\CommandLine
     */
    public function hello($name, Output $output)
    {
        $output->writeln('Hello ' . $name . ' !');
    }
}