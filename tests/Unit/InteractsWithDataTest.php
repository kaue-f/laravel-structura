<?php

namespace Tests\Unit;

use KaueF\Structura\Concerns\InteractsWithData;
use Tests\TestCase;

readonly class UserData
{
    use InteractsWithData;

    public function __construct(
        public string $name,
        public string $email,
    ) {}
}

class InteractsWithDataTest extends TestCase
{
    public function test_make_creates_data_from_constructor_arguments(): void
    {
        $data = UserData::make('Jane Doe', 'jane@example.com');

        $this->assertInstanceOf(UserData::class, $data);
        $this->assertSame('Jane Doe', $data->name);
        $this->assertSame('jane@example.com', $data->email);
    }
}
