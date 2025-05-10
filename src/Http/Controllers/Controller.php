<?php

namespace Itstudioat\Spa\Http\Controllers;

use Itstudioat\Spa\Traits\HasRoleTrait;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use HasRoleTrait;
}
