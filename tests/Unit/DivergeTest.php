<?php

namespace Tests\Unit;

use App\Rules\DivergeDifferenceRule;
use Tests\TestCase;
use App\Validators\DivergeValidator;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * @class DivergeTest
 * @package Tests\Unit
 */
class DivergeTest extends TestCase
{
    /**
     * @const int
     */
    const ACCEPTABLE_DEVIATION_PERCENTAGE = 10;

    /**
     * Тестирование корректности работы валидатора
     *
     * @return void
     */
    public function test_diff_price(): void
    {
        $validator = new DivergeValidator(self::ACCEPTABLE_DEVIATION_PERCENTAGE);

        $this->assertTrue($validator->diffPrice(550, 500));
        $this->assertFalse($validator->diffPrice(600, 500));
        $this->assertFalse($validator->diffPrice(3, 4));
    }

    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Throwable
     */
    public function test_get_deviation(): void
    {
        $validator = new DivergeValidator(self::ACCEPTABLE_DEVIATION_PERCENTAGE);

        try {
            $this->assertEquals(10, $validator->getDeviation());
        } catch (Throwable $ex) {
            $this->assertEquals('Deviation must be calculated at first', $ex->getMessage());
        }

        $validator->diffPrice(550, 500);
        $this->assertEquals(10, $validator->getDeviation());

        $validator->diffPrice(3, 4);
        $this->assertEquals(25, $validator->getDeviation());
    }

    /**
     * Валидация с некорректными данными
     *
     * @return void
     * @throws Throwable
     */
    public function test_validation_invalid(): void
    {
        $data = [
            'new' => 1,
            'out' => 4,
            'acceptableDeviation' => 10,
        ];

        $validator = Validator::make($data, [
            'out' => 'required',
            'acceptableDeviation' => 'required',
            'new' => [
                'required',
                new DivergeDifferenceRule($data['acceptableDeviation'], $data['new'], $data['out']),
            ],
        ]);

        try {
            $validator->validate();
        } catch (Throwable $ex) {
            $this->assertEquals(1, $validator->errors()->count());
            $this->assertEquals('Отклонение цены на 75% превышает допустимые 10%', $ex->getMessage());
        }
    }

    /**
     * Валидация с корректными данными
     *
     * @return void
     * @throws Throwable
     */
    public function test_validation_valid(): void
    {
        $data = [
            'new' => 3,
            'out' => 4,
            'acceptableDeviation' => 25,
        ];

        $validator = Validator::make($data, [
            'out' => 'required',
            'acceptableDeviation' => 'required',
            'new' => [
                'required',
                new DivergeDifferenceRule($data['acceptableDeviation'], $data['new'], $data['out']),
            ],
        ]);

        $validator->validate();

        $this->assertEquals(0, $validator->errors()->count());
    }
}
