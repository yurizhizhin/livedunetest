<?php

namespace App\Rules;

use App\Validators\DivergeValidator;
use Illuminate\Contracts\Validation\Rule;
use Exception;

/**
 * @class DivergeDifferenceRule
 * @package App\Rules
 */
class DivergeDifferenceRule implements Rule
{
    /**
     * @var float Новая цена
     */
    private float $new;

    /**
     * @var float Текущая цена
     */
    private float $out;

    /**
     * @var float Допустимое отклонение
     */
    private float $acceptableDeviation;

    /**
     * @var DivergeValidator
     */
    private DivergeValidator $validator;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(float $acceptableDeviation, float $new, float $out)
    {
        $this->acceptableDeviation = $acceptableDeviation;
        $this->validator = new DivergeValidator($this->acceptableDeviation);
        $this->new = $new;
        $this->out = $out;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->validator = new DivergeValidator($this->acceptableDeviation);

        return $this->validator->diffPrice($this->new, $this->out);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     * @throws Exception
     */
    public function message(): string
    {
        return "Отклонение цены на {$this->validator->getDeviation()}% превышает допустимые {$this->acceptableDeviation}%";
    }
}