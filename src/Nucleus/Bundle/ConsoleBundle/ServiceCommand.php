<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Nucleus\Bundle\ConsoleBundle;
use Nucleus\Invoker\IInvoker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\ConsoleBundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of ServiceCommand
 *
 * @author AxelBarbier
 */
class ServiceCommand extends Command
{
    private $shortDesc;
    private $paramsArray;
    
    private $serviceName;
    private $serviceMethod;
    
    /**
     * @var IInvokerService 
     */
    private $invoker;


    /**
     * 
     * @param string $name
     * @param string $shortDesc
     * @param array $paramsArray
     */
    public function __construct(
        $name,
        $shortDesc,
        $paramsArray,
        $serviceName,
        $serviceMethod,
        IInvoker $invoker,
        ContainerInterface $container
    )
{
        $this->shortDesc     = $shortDesc;
        $this->paramsArray   = $paramsArray;
        $this->serviceName   = $serviceName;
        $this->serviceMethod = $serviceMethod;
        $this->invoker       = $invoker;
        $this->container     = $container;
        parent::__construct($name);
    }

    /**
     * Extends configure from Symfony Command Class
     */
    protected function configure()
    {
        $this->setDescription($this->shortDesc);

        foreach($this->paramsArray as $nameParam => $arrayOptions){
            if($arrayOptions['optional']){
                $this->addOption($nameParam, null, InputOption::VALUE_OPTIONAL, $arrayOptions['comment'], null);
            }
            else {
                $this->addOption($nameParam, null, InputOption::VALUE_REQUIRED, $arrayOptions['comment'], null);
            } 
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        $parameters = $input->getOptions();
        
        foreach($parameters as $optionName => $optionValue){
            if(is_null($optionValue)){
                unset($parameters[$optionName]);
            }
        }

        $parameters[] = $input;
        $parameters[] = $output;

        $this->invoker->invoke(
            array($this->container->get($this->serviceName),$this->serviceMethod),
            $parameters
        );
    }
}
