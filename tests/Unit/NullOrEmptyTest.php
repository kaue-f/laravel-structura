<?php

namespace Tests\Unit;

use Tests\TestCase;

class NullOrEmptyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test value is null.
     * 
     * Result from test = No
     * Result from test 1 = null
     * 
     * @return void
     */
    public function test_null(): void
    {
        $value = 'test';
        $result = ($value === null);

        $value_1 = null;
        $result_1 = ($value_1 === null);

        $this->assertNotTrue($result);
        $this->assertTrue($result_1);
    }

    /**
     * Test value string is empty.
     * 
     * Result from test = True
     * Result from test 1 = True
     * Result from test 2 = No
     * Result from test 3 = No
     * Result from test 4 = True
     * Result from test 5 = True
     * Result from test 6 = No
     * 
     * 
     * @return void
     */
    public function test_empty_string(): void
    {
        $value = '';
        $result = is_string($value);

        $value_1 = 'test';
        $result_1 = is_string($value_1);

        $value_2 = 'test';
        $result_2 = empty($value_2);

        $value_3 = ' test ';
        $result_3 = (trim($value_3) === '');

        $value_4 = '';
        $result_4 = empty($value_4);

        $value_5 = ' ';
        $result_5 = (trim($value_5) === '');

        $value_6 = '  ';
        $result_6 = empty($value_6);

        $this->assertTrue($result);
        $this->assertTrue($result_1);
        $this->assertNotTrue($result_2);
        $this->assertNotTrue($result_3);
        $this->assertTrue($result_4);
        $this->assertTrue($result_5);
        $this->assertNotTrue($result_6);
    }

    /**
     * Test value array is empty.
     * 
     * Result from test = True
     * Result from test 1 = True
     * Result from test 2 = True
     * Result from test 3 = No
     * Result from test 4 = No
     * 
     * @return void
     */
    public function test_array(): void
    {
        $value = [];
        $result = is_array($value);

        $value_1 = [1, 2, 3];
        $result_1 = is_array($value_1);

        $value_2 = [];
        $result_2 = count($value_2) === 0;

        $value_3 = [1, 2, 3];
        $result_3 = count($value_3) === 0;

        $value_4 = '[]';
        $result_4 = is_array($value_4);

        $this->assertTrue($result);
        $this->assertTrue($result_1);
        $this->assertTrue($result_2);
        $this->assertNotTrue($result_3);
        $this->assertNotTrue($result_4);
    }
}
