nucleus-console-bundle
======================

Using a annotation above a service method you are able to create a command accessible from the CLI.

To use it in your application you must register 2 bundles since there is a dependency on [nucleus-bundle](https://github.com/mpoiriert/nucleus-bundle).

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Nucleus\Bundle\CoreBundle\NucleusCoreBundle(),
        new Nucleus\Bundle\BinderBundle\NucleusConsoleBundle(),
        // ...
    );

When this is done you can use the Nucleus\Console\CommandLine annotation above any public method of your service.

    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    class TestService
    {
        /**
         * Comment from the method
         *
         * @\Nucleus\Console\CommandLine(name="my:test:hello")
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

The description of the command will be parse from the first line of the phpDoc. All the argument are treat as option
(meaning you need to set it with --optionName), you can define a default value if the option is optional. The comment
in front of the @param will be use by the --help of you command. You don't have any interface to respect, like
Controller in symfony all the parameter will be inject according to it's name or strong typing. Since the service
doesn't have to extend the Command class of symfony you can inject a "parentCommand" from where you can access everything
publicly available from the Command object (like the application if you want to access helper...).

If you don't specify a name attribute to the annotation the name of the service follow by the method name will be used.
