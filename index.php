<?php
define('START_TIME', microtime(true));
define('ROOT',__DIR__);
require __DIR__.'/framework/Base.php';
(new \framework\Base())->run();