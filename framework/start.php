<?php
namespace framework;
// 1. 加载基础类
require ROOT . '/framework/library/Base.php';
// 2. 执行应用
(new \framework\library\Base())->run();
