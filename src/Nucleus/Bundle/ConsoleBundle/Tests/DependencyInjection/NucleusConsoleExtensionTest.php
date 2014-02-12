<?php

namespace Nucleus\Bundle\BinderBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NucleusConsoleExtensionTest extends WebTestCase
{
    /**
     * @outputBuffering disabled
     */
    public function test()
    {
        $client = static::createClient();

        $this->expectOutputString('toto');

        $application = new Application($client->getKernel());

        $application->run(new ArgvInput(array('','test_service:hello','--name=toto')));


    }
}