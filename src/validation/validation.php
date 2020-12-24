<?php

namespace PhpCliLibrary\Validation;

use PhpCliLibrary\Commands\Command;


class Validation
{
    public $inputData;
    public $case;
    public $commandList;
    public $validData = [];



    public function __construct($inputData)
    {
        $this->inputData = (array) $inputData;
        $this->commandList = json_decode(json_encode(Command::commandMap()),TRUE);
    }


    public function validate()
    {
        $matches  = array_intersect($this->inputData, array_keys($this->commandList));

        if(count($matches)>0) {
            $this->validData['command'] = current($matches);
            $this->case = $this->validateOptions();
        } else {
            $this->case = -1;
        }

        return $this->case;

    }


    public function validateOptions()
    {
        $isSomethingWrongInArray = array_diff($this->inputData, preg_grep("/^{([\s\S]+?)}+$|^\[([\s\S]+?)\]+$|app.php|{$this->validData['command']}/", $this->inputData));
        if(count($isSomethingWrongInArray) > 0)
            return 0;

        return ($this->validateAttributes() && $this->validateParameters());
    }


    public function validateAttributes()
    {
        $arrInputArguments = $this->regular("/^{([\s\S]+?)}+$/", ",", $this->inputData);
        $arrCommandArguments = $this->commandList[$this->validData['command']]['arguments'];

        if(!array_diff($arrInputArguments, array_keys($arrCommandArguments))) {
            if(in_array('help', $arrInputArguments)) return 0;
            $this->validData['arguments'] = $arrInputArguments;
            return 1;
        }

        return 0;

    }


    public function validateParameters()
    {
        $params = $this->squareBrackets();

        // Проверить что все параметры совпадают с json
        $arrCommandParameters = array_keys($this->commandList[$this->validData['command']]['parameters']);

        if(!array_diff(array_keys($params), $arrCommandParameters)) {
            // Параметры совпадают. Проверить на валидность ввода значения параметров
            if(preg_grep("/^[A-Za-z0-9]+$|^{([\s\S]+?)}+$/", $params)) {
                // Совпадют с регулярным выражением
                // Проверить есть ли массив в значении и обработать его
                $arrayInValue = preg_grep("/^{([\s\S]+?)}+$/", $params);
                if(count($arrayInValue) > 0) {
                    foreach ($arrayInValue as $key=>$val) {
                        $params[$key] = explode(',', substr($val, 1, (strlen($val)-2)));
                    }
                }

                $this->validData['parameters'] = $params;
                return 1;

            } else {
                return 0;
            }
        } else {
            return 0;
        }

    }


    public function regular($regular, $delimeter, $arr) {
        $args = preg_grep($regular, $arr);
        $args = array_map(
            function($arg) use ($delimeter) {
                return explode($delimeter, substr($arg, 1, (strlen($arg)-2)));
            }, $args
        );

        return call_user_func_array('array_merge',$args);
    }


    public function squareBrackets()
    {
        $params = preg_grep("/^\[([\s\S]+?)\]+$/", $this->inputData);
        array_walk($params,
            function(&$value) {
                $array = explode('=', substr($value, 1, (strlen($value)-2)));
                $value = array($array[0] => $array[1]);
            });

        return call_user_func_array('array_merge',$params);
    }

}
