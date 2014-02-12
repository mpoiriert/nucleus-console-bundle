<?php

namespace Nucleus\Bundle\ConsoleBundle\DependencyInjection;

use Nucleus\Bundle\CoreBundle\DependencyInjection\GenerationContext;
use Nucleus\Bundle\CoreBundle\DependencyInjection\IAnnotationContainerGenerator;
use Sami\Parser\DocBlockParser;
use Symfony\Component\DependencyInjection\Definition;
use Nucleus\Invoker\IInvoker;

/**
 * @author AxelBarbier
 * @author Martin Poirier Theoret <mpoiriert@gmail.com>
 */
class CommandLineAnnotationContainerGenerator implements IAnnotationContainerGenerator
{
    public function processContainerBuilder(GenerationContext $context)
    {
        $annotation = $context->getAnnotation();

        /* @var $annotation \Nucleus\Console\CommandLine */
        $docParser      = new DocBlockParser();
        $serviceName    = $context->getServiceName();
        $methodName     = $context->getParsingContextName();
        $definition     = $context->getContainerBuilder()->getDefinition($serviceName);
        $shortDesc      = 'N/A';
        $reflectedMethod = new \ReflectionMethod($definition->getClass(), $methodName);
        $methodComment = $reflectedMethod->getDocComment();
        
        if ($methodComment !== false) {
            $docMethod = $docParser->parse($methodComment);
            $shortDesc  = $docMethod->getShortDesc();
        }
        $paramsArray = array();
        $paramArrayComments = self::extractParamDocComment($docMethod->getTag('param'));
        
        foreach($reflectedMethod->getParameters() as $reflectionParameter){
            $paramComment = 'N/A';
            if(isset($paramArrayComments[$reflectionParameter->getName()])){
                $paramComment = $paramArrayComments[$reflectionParameter->getName()]['comment'];
            }
            
            $paramsArray[$reflectionParameter->getName()]['optional'] = false;
            if ($reflectionParameter->isDefaultValueAvailable()) {
                $paramsArray[$reflectionParameter->getName()]['optional'] = true;
            }
            $paramsArray[$reflectionParameter->getName()]['comment'] = $paramComment;
        }
        if(!empty($annotation->name)){
            $name = $annotation->name;
        } else {
            $name = $serviceName.':'.$methodName;
        }

        $commands = array();// $context->getContainerBuilder()->getParameter('nucleus.console.commands');
        if(!$commands) {
            $commands = array();
        }
        $commands[] = compact('name','shortDesc','paramsArray','serviceName','methodName');
        $context->getContainerBuilder()->setParameter('nucleus.console.commands', $commands);
    }
    
    private function extractParamDocComment($tagArray){
        $paramArray = array();
        if(!is_array($tagArray)){
            return false;
        }
        foreach($tagArray as $tag){
            if(is_array($tag)){
                $paramArray[$tag[1]] = array();
                if(isset($tag[2]) && !empty($tag[2]))
                    $paramArray[$tag[1]]['comment'] = $tag[2];
                else
                    $paramArray[$tag[1]]['comment'] = 'N/A';
            }
        }
        return $paramArray;
    }
}

