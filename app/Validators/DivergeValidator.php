<?php

namespace App\Validators;

use App\Interfaces\Validators\DivergeInterface;
use Exception;
/**
 * @class DeviationValidator
 * @package App\Validators
 */
class DivergeValidator implements DivergeInterface
{
    /**
     * @const int
     */
    const PERCENTAGE_NUMBER = 100;

    /**
     * @var float Допустимое отклонение в %
     */
    private float $acceptableDeviation;

    /**
     * @var float|null Результат отклонения в %
     */
    private ?float $deviation = null;

    /**
     * @param float $acceptableDeviation
     */
    public function __construct(float $acceptableDeviation)
    {
        $this->acceptableDeviation = $acceptableDeviation;
    }

    /**
     * {@inheritDoc}
     */
    public function diffPrice(float $new, float $out): bool
    {
        // вычисляем абсолютную разницу в % отклонения цены (как скидки, так и наценки)
        $this->deviation = abs(round(($new / $out - 1) * self::PERCENTAGE_NUMBER, 2));

        return $this->deviation <= $this->acceptableDeviation;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function getDeviation(): float
    {
        return is_null($this->deviation) ?
            throw new Exception('Deviation must be calculated at first') : $this->deviation;
    }
}