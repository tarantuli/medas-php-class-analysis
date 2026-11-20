<?php

namespace App;

use Vendor\Package;
use Vendor\Other as Alias;

class Consumer
{
    public function make(): Package\Client
    {
        return new Alias\Factory();
    }

    public function local(): Sub\Local
    {
        return new Sub\Local();
    }
}
