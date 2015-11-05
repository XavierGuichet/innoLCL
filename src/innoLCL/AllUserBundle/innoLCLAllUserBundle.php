<?php

namespace innoLCL\AllUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class innoLCLAllUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
