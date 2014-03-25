<?php

namespace Nucleus\Bundle\ConsoleBundle\Command;

/**
 * @author Martin Poirier Theoret <mpoiriert@gmail.com>
 * 
 * @Annotation
 * 
 * @Target({"METHOD"})
 */
class CommandLine
{
    /**
     * Will be the name for command 
     * @var string  
     */
    public $name;
}
