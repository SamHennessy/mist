<?php

namespace Mist\Log\_1_0;

interface Logger extends \Psr\Log\LoggerInterface
{
    public function setBuilder(\Closure $builder);

    public function getLogger();
}
