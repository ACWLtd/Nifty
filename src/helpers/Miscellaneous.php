<?php namespace Kjamesy\Cms\Helpers;

use Illuminate\Support\Facades\Validator;

class Miscellaneous
{
    public static function validate($inputs, $rules)
    {
        $validation = Validator::make($inputs, $rules);

        return $validation->fails() ? $validation->messages() : true;
    }
}
