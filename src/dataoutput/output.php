<?php

namespace PhpCliLibrary\Dataoutput;


use PhpCliLibrary\Commands\Command;
use PhpCliLibrary\Validation\Validation;


class Output
{
    public $validation;

    public function __construct($inputData) {
        $this->validation = new Validation($inputData);
    }

    public function calledCommand() {
        $output = 'Called command: '.$this->validation->validData['command'];

        $output .= "\n\n";

        if(count($this->validation->validData['arguments']) > 0) {
            $output .= 'Arguments:'."\n\t";
            foreach ($this->validation->validData['arguments'] as $argument) {
                $output .= '- '.$argument."\n\t";
            }
        }

        $output .= "\n";

        if(count($this->validation->validData['parameters']) > 0) {
            $output .= 'Parameters:'."\n\t";
            foreach ($this->validation->validData['parameters'] as $key => $parameter) {
                if(is_array($parameter)) {
                    $output .= '- ' . $key . "\n\t";
                    foreach ($parameter as $param) {
                        $output .= "\t".'- ' . $param . "\n\t";
                    }
                } else {
                    $output .= '- ' . $key . "\n\t\t";
                    $output .= '- ' . $parameter . "\n\t";
                }
            }
        }
        $output .= "\n";
        return $output;
    }


    public function helpCommandDescription()
    {
        $output = 'Command help:'."\n\t";
        $output .= $this->help($this->validation->validData['command']);

        return $output;
    }


    public function helpDescription()
    {
        $output = 'Commands list help:'."\n\t";
        $output .= $this->help();

        return $output;
    }


    public function help($command_name = false)
    {
        $commands = Command::commandMap();

        foreach ($commands as $command => $commandsData) {
            if($command_name && $command_name !== $command) continue;

            $output = 'Command name:'."\n\t\t".'- '.$command."\n\t";
            $output .= 'Command description:'."\n\t\t".'- '.$commandsData->description."\n\t";

            $output .= 'Command arguments: '."\n\t\t";
            foreach ($commandsData->arguments as $arg => $desc) {
                $output .= $arg. ': '."\n\t\t\t".'- '.$desc->description."\n\t\t";
            }

            $output .= "\r\n\t";

            $output .= 'Command parameters: '."\n\t\t";
            foreach ($commandsData->parameters as $param => $obj) {
                $output .= $param. ': '."\n\t\t\t".'- '.$obj->description."\n\t\t";
                if(isset($obj->value)) {
                    $output .= "\t".'- value'."\n\t\t\t\t";
                    if (is_array($obj->value)) {
                        foreach ($obj->value as $val) {
                            $output .= '- '.$val."\n\t\t\t\t";
                        }
                    } else {
                        $output .= '- '.$obj->value."\n\t\t\t\t";
                    }

                    $output .= "\r\t\t";
                }
            }

            if(isset($obj->default_value)) {
                $output .= "\r\t\t\t".'- default_value'."\n\t\t\t\t". '- '.$obj->default_value;
            }

            $output .= "\r\n\n\t";
        }

        return $output;
    }


}
