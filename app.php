<?php

use Christophedlr\Pharcreator\CreatePharCommand as CreatePharCommandAlias;
use Symfony\Component\Console\Application;

require_once __DIR__.'/vendor/autoload.php';

$application = new Application();

$create = new CreatePharCommandAlias();

$application->add($create);
$application->run();
