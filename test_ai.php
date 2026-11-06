<?php
require 'vendor/autoload.php';
\ = require_once 'bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();
\ = app(App\Services\FallbackAiService::class);
\ = \->ask('hi');
echo substr(\, 0, 80);
