<?php

namespace Tests\Unit;

use KaueF\Structura\Concerns\Makeable;
use RuntimeException;
use Tests\TestCase;

class ExecuteAction
{
    use Makeable;

    public function execute(int $number)
    {
        return $number * 2;
    }
}

class HandleAction
{
    use Makeable;

    public function handle(string $text)
    {
        return strtoupper($text);
    }
}

class InvokeAction
{
    use Makeable;

    public function __invoke()
    {
        return 'invoked';
    }
}

class BadAction
{
    use Makeable;
}

class MakeableTest extends TestCase
{
    public function test_makeable_resolves_execute()
    {
        $this->assertEquals(4, ExecuteAction::run(2));
    }

    public function test_makeable_resolves_handle()
    {
        $this->assertEquals('HELLO', HandleAction::run('hello'));
    }

    public function test_makeable_resolves_invoke()
    {
        $this->assertEquals('invoked', InvokeAction::run());
    }

    public function test_makeable_throws_exception_if_no_method()
    {
        $this->expectException(RuntimeException::class);
        BadAction::run();
    }

    public function test_makeable_resolves_custom_method()
    {
        $this->assertEquals('magic', CustomAction::run());
    }

    public function test_makeable_throws_exception_if_custom_method_missing()
    {
        $this->expectException(RuntimeException::class);
        BadCustomAction::run();
    }
}

class CustomAction
{
    use Makeable;

    protected string $makeableMethod = 'processMagic';

    public function processMagic()
    {
        return 'magic';
    }
}

class BadCustomAction
{
    use Makeable;

    protected string $makeableMethod = 'doesNotExist';
}
