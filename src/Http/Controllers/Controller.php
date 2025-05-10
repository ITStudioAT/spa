<?php

namespace Itstudioat\Spa\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Itstudioat\Spa\Traits\HasRoleTrait;

class Controller extends BaseController
{
    use HasRoleTrait;
}
