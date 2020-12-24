<?php


namespace PhpCliLibrary\Action;



class Action
{
    public $output;

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function run()
    {
        switch ($this->output->validation->validate()) {
            case 0:
                return $this->output->helpCommandDescription();

            case 1:
                return $this->output->calledCommand();

            case -1:
                return $this->output->helpDescription();
        }
    }

}