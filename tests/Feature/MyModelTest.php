<?php

use Itstudioat\Spa\Models\MyModel;



it('can return uppercase name', function () {
    $myModel = MyModel::factory()->create(['name' => 'John']);

    expect($myModel->getUppercasedName())->toEqual('JOHN');
});
