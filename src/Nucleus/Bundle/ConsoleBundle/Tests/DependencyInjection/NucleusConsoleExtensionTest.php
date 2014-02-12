<?php

namespace Nucleus\Bundle\BinderBundle\Tests\DependencyInjection;

use Nucleus\Bundle\ConsoleBundle\DependencyInjection\CommandLineAnnotationContainerGenerator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NucleusConsoleExtensionTest extends WebTestCase
{
    public function provideTestParseBlockComment()
    {
        return array(
            array(
                '/**
                 * Comment from the method
                 *
                 * @Nucleus\Console\CommandLine
                 * @param string $name The name of the person you want to say hello to
                 * @param Output $output
                 */',
                'Comment from the method',
                array(
                    'name'=>'The name of the person you want to say hello to',
                    'output' => 'N/A'
                ),
            ),
            array(
               '/**
                 *
                 *
                 * @Nucleus\Console\CommandLine
                 * @param string $name The name of the person you want to say hello to
                 * @param Output $output
                 */',
                'N/A',
                array(
                    'name'=>'The name of the person you want to say hello to',
                    'output' => 'N/A'
                ),
            ),
            array(
                '/**
                  * @param string $name The name of the person you want to say hello to
                  * @param Output $output
                  * @Nucleus\Console\CommandLine
                  */',
                'N/A',
                array(
                    'name'=>'The name of the person you want to say hello to',
                    'output' => 'N/A'
                ),
            ),
            array(
                '/**
                  * @param string $name The name of the person you want to say hello to
                  * another line that should be ignore
                  * @param Output $output
                  */',
                'N/A',
                array(
                    'name'=>'The name of the person you want to say hello to',
                    'output' => 'N/A'
                ),
            )
        );
    }

    /**
     * @dataProvider provideTestParseBlockComment
     */
    public function testParseBlockComment($comment, $expectedDescription,$expectedParameters)
    {
        list($description, $parameters) = CommandLineAnnotationContainerGenerator::parseBlockDocComment($comment);
        $this->assertEquals($expectedDescription,$description);
        $this->assertEquals($expectedParameters,$parameters);
    }

    public function testIntegration()
    {
        $client = static::createClient();

        $application = new Application($client->getKernel());

        $output = new BufferedOutput();
        $application->setAutoExit(false);


        $application->run(new ArgvInput(),$output);
        $this->assertTrue(strpos($output->fetch(),'Comment from the method') !== false,'Description of the command is not parse from the comment of the method');

        $arguments = array(
            'this will be ignored',
            'test_service:hello',
            '--name=toto',
            '--help'
        );

        $application->run(new ArgvInput($arguments),$output);
        $outputString = $output->fetch();
        $this->assertTrue(strpos($outputString,'The name of the person you want to say hello to') !== false,'Parsing of param comment is not working');
        $this->assertTrue(strpos($outputString,'--output') === false,'Output parameter should not be there since it cannot be enter by the user');
    }
}