<?php

spl_autoload_register(function($worker) {
    $path = __DIR__ . DS . 'workers' . DS . 'engine' . DS . 'kernel' . DS . strtolower($worker) . '.php';
    if(file_exists($path)) require $path;
});

foreach(glob(__DIR__ . DS . 'workers' . DS . 'engine' . DS . 'plug' . DS . '*.php') as $plug) {
    require $plug;
}

Weapon::add('shell_after', function() use($config) {
    echo Asset::stylesheet(array(
        __DIR__ . DS . 'assets' . DS . 'shell' . DS . 'calendar.css',
        __DIR__ . DS . 'assets' . DS . 'shell' . DS . 'calendar.' . $config->shield . '.css'
    ), "", 'shell/calendar.min.css');
});