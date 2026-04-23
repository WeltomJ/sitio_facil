<?php

$env = require __DIR__ . '/env.php';
$cfg = $env['app'][SF_AMBIENTE];

return [
    'name'     => 'Sítio Fácil',
    'url'      => $cfg['url'],
    'debug'    => $cfg['debug'],
    'timezone' => 'America/Manaus',
    'session'  => [
        'name'     => 'sitio_facil_session',
        'lifetime' => 7200,
    ],
];
