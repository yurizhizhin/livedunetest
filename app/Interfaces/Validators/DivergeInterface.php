<?php

namespace App\Interfaces\Validators;

/**
 * @interface DivergeInterface
 * @package App\Interfaces\Validators
 */
interface DivergeInterface
{
    /**
     * Отклонение цены не должно быть больше допустимого значения (%)
     *
     * @param float $new Новая цена, которую будем проверять на отклонение.
     * @param float $out Текущая цена.
     * @return bool
     */
    public function diffPrice(float $new, float $out): bool;

    /**
     * Результат отклонения в %
     *
     * @return float
     */
    public function getDeviation(): float;
}