<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OneWord implements Rule
{


    private $customAttributeName = null;

    /**
     * Create a new rule instance.
     *
     * @param string $customAttributeName
     */
    public function __construct($customAttributeName = "")
    {
        $this->customAttributeName = $customAttributeName;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return count(explode(" ", $value)) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The ' . ($this->customAttributeName ?? ':attribute') . ' field has to have only one word';
    }
}
