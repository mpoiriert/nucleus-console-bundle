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
     * Comment from the method
     *
     * @\Nucleus\Console\CommandLine
     * @param string $name The name of the person you want to say hello to
     * @param Output $output
     */
    public function hello($name, Output $output)
    {
        $output->write('Hello ' . $name . ' !');
    }
}