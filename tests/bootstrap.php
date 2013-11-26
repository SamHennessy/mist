<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('MistTest\\', __DIR__);

assert_options(ASSERT_ACTIVE,   true);
assert_options(ASSERT_BAIL,     false);
assert_options(ASSERT_WARNING,  true);
