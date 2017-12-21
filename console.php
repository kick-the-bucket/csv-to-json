#!/usr/bin/env php
<?php

use CsvToJson\Command\ConvertCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$app = new Application('CsvToJson');
$app->add(new ConvertCommand());
$app->run();
