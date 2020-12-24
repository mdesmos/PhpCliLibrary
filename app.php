<?php

require __DIR__ . '/vendor/autoload.php';

use PhpCliLibrary\Action\Action;
use PhpCliLibrary\Dataoutput\Output;


$app = new Action(new Output($argv));
echo $app->run();

