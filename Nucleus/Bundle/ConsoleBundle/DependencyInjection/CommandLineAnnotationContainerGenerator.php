<?php

namespace Nucleus\Bundle\ConsoleBundle\DependencyInjection;

use Nucleus\Bundle\CoreBundle\DependencyInjection\GenerationContext;
use Nucleus\Bundle\CoreBundle\DependencyInjection\IAnnotationContainerGenerator;

/**
 * @author AxelBarbier
 * @author Martin Poirier Theoret <mpoiriert@gmail.com>
 */
class CommandLineAnnotationContainerGenerator implements IAnnotationContainerGenerator
{
    const CONTAINER_COMMANDS_PARAMETER = 'nucleus.console.commands';

    public function processContainerBuilder(GenerationContext $context)
    {
        $annotation = $context->getAnnotation();

        /* @var $annotation \Nucleus\Console\CommandLine */
        $serviceName    = $context->getServiceName();
        $methodName     = $context->getParsingContextName();
        $definition     = $context->getContainerBuilder()->getDefinition($serviceName);
        $shortDesc      = 'N/A';
        $reflectedMethod = new \ReflectionMethod($definition->getClass(), $methodName);
        $methodComment = $reflectedMethod->getDocComment();
        $paramArrayComments = array();
        
        if ($methodComment !== false) {
            list($shortDesc, $paramArrayComments) = $this->parseBlockDocComment($methodComment);
        }

        $paramsArray = array();

        foreach($reflectedMethod->getParameters() as $reflectionParameter){
            if($reflectionParameter->getClass()) {
                continue;
            }
            $paramComment = 'N/A';
            if(isset($paramArrayComments[$reflectionParameter->getName()])){
                $paramComment = $paramArrayComments[$reflectionParameter->getName()];
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

        if($context->getContainerBuilder()->hasParameter(self::CONTAINER_COMMANDS_PARAMETER)) {
            $commands = $context->getContainerBuilder()->getParameter(self::CONTAINER_COMMANDS_PARAMETER);
        } else {
            $commands = array();
        }

        $commands[] = compact('name','shortDesc','paramsArray','serviceName','methodName');
        $context->getContainerBuilder()->setParameter(self::CONTAINER_COMMANDS_PARAMETER, $commands);
    }

    static public function parseBlockDocComment($comment)
    {
        $comment = preg_replace(array('#^/\*\*\s*#', '#\s*\*/$#', '#^\s*\*#m'), '', trim($comment));
        $comment = "\n".preg_replace('/(\r\n|\r)/', "\n", $comment);

        $short = '';
        if (preg_match('/(.*?)(\n[ \t]*@([^ ]+)(?:\s+(.*?))?(?=(\n[ \t]*@|\s*$))|$)/As', $comment, $match)) {
            $comment = substr($comment,strlen($match[1]));
            $short = trim($match[1]);
            $long = '';

            // short desc ends at the first dot or when \n\n occurs
            if (preg_match('/(.*?)(\.\s|\n\n|$)/s', $short, $match)) {
                $long = trim(substr($short, strlen($match[0])));
                $short = trim($match[0]);
            }
        }

        $parameters = array();

        while(preg_match('(@param.*|\s$)', $comment, $match)) {
            $comment = str_replace($match[0],'',$comment);
            @list(,,$parameterName,$parameterDescription) = explode(' ',$match[0],4);
            if($parameterName) {
                if(!$parameterDescription) {
                    $parameterDescription = 'N/A';
                }
                $parameters[substr($parameterName,1)] = $parameterDescription;
            }
            $match = array();
        }

        if(empty($short)) {
            $short = 'N/A';
        }

        return array($short, $parameters);
    }
}

