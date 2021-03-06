<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Utilities;

use PublishingKit\Utilities\Str;
use Tests\SimpleTestCase;
use ReflectionClass;

final class StrTest extends SimpleTestCase
{
    /**
     * @var Str
     */
    private $str;

    protected function setUp(): void
    {
        $str = 'I am the very model of a modern major general';
        $this->str = new Str($str);
    }

    protected function tearDown(): void
    {
        $this->str = null;
        $reflect = new ReflectionClass(Str::class);
        $macros = $reflect->getProperty('macros');
        $macros->setAccessible(true);
        $macros->setValue(null);
    }

    public function testImplementsCountable()
    {
        $this->assertInstanceOf('Countable', $this->str);
    }

    public function testCanCountCorrectly()
    {
        $this->assertSame(45, $this->str->count());
    }

    public function testCanBeCalledStatically()
    {
        $str = 'I am the very model of a modern major general';
        $this->str = Str::make($str);
        $this->assertSame(45, $this->str->count());
    }

    public function testImplementsArrayAccess()
    {
        $this->assertInstanceOf('ArrayAccess', $this->str);
    }

    public function testCanConfirmOffsetExists()
    {
        $this->assertTrue($this->str->offsetExists(0));
    }

    public function testCanGetOffset()
    {
        $this->assertSame('I', $this->str->offsetGet(0));
    }

    public function testCanSetOffset()
    {
        $this->str->offsetSet(0, 'A');
        $this->assertSame('A', $this->str->offsetGet(0));
    }

    public function testAppendsElementWhenOffsetSetPassedNull()
    {
        $this->str->offsetSet(null, 'B');
        $this->assertSame('I', $this->str->offsetGet(0));
        $this->assertSame('B', $this->str->offsetGet(45));
    }

    public function testCanUnsetOffset()
    {
        $this->str->offsetUnset(1);
        $this->assertSame('a', $this->str->offsetGet(1));
        $this->assertSame(44, $this->str->count());
    }

    public function testImplementsTraversable()
    {
        $this->assertInstanceOf('Traversable', $this->str);
    }

    public function testImplementsIterator()
    {
        $this->assertInstanceOf('Iterator', $this->str);
    }

    public function testCanGetCurrentPosition()
    {
        $this->assertSame('I', $this->str->current());
    }

    public function testCanGetKey()
    {
        $this->assertSame(0, $this->str->key());
    }

    public function testCanMoveForward()
    {
        $this->assertSame(0, $this->str->key());
        $this->str->next();
        $this->assertSame(1, $this->str->key());
    }

    public function testCanRewind()
    {
        $this->str->next();
        $this->assertSame(1, $this->str->key());
        $this->str->rewind();
        $this->assertSame(0, $this->str->key());
    }

    public function testCanValidate()
    {
        $this->assertTrue($this->str->valid());
    }

    public function testRendersToString()
    {
        $this->assertSame('I am the very model of a modern major general', $this->str->__toString());
    }

    public function testCanReplace()
    {
        $this->assertSame(
            'I am the very model of a scientist Salarian',
            $this->str->replace('modern major general', 'scientist Salarian')->__toString()
        );
    }

    public function testCanConvertToUpper()
    {
        $this->assertSame('I AM THE VERY MODEL OF A MODERN MAJOR GENERAL', $this->str->toUpper()->__toString());
    }

    public function testCanConvertToLower()
    {
        $this->assertSame('i am the very model of a modern major general', $this->str->toLower()->__toString());
    }

    public function testCanTrim()
    {
        $str = '  I am the very model of a modern major general  ';
        $this->str = new Str($str);
        $this->assertSame('I am the very model of a modern major general', $this->str->trim()->__toString());
    }

    public function testCanLtrim()
    {
        $str = '  I am the very model of a modern major general  ';
        $this->str = new Str($str);
        $this->assertSame('I am the very model of a modern major general  ', $this->str->ltrim()->__toString());
    }

    public function testCanRtrim()
    {
        $str = '  I am the very model of a modern major general  ';
        $this->str = new Str($str);
        $this->assertSame('  I am the very model of a modern major general', $this->str->rtrim()->__toString());
    }

    public function testPath()
    {
        $str = new Str('\foo\bar');
        $this->assertSame(DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar', $str->path()->__toString());
    }

    public function testSupportsMacros()
    {
        $this->str->macro('bananas', function () {
            return 'bananas';
        });
        $this->assertSame('bananas', $this->str->bananas());
    }

    public function testSupportsStaticMacros()
    {
        Str::macro('bananas', function () {
            return 'bananas';
        });
        $str = 'I am the very model of a modern major general  ';
        $this->str = new Str($str);
        $this->assertSame('bananas', $this->str->bananas());
    }

    public function testSupportsCallingMacrosStatically()
    {
        Str::macro('bananas', function () {
            return 'bananas';
        });
        $this->assertSame('bananas', Str::bananas());
    }

    public function testAbsentMacroMethod()
    {
        $this->expectException('BadMethodCallException');
        $str = new Str('I am the very model of a modern major general');
        $str->foo();
    }

    public function testAbsentMacroMethodStatic()
    {
        $this->expectException('BadMethodCallException');
        Str::foo();
    }

    public function testMixinFromClass()
    {
        Str::mixin(new class {
            public function foo()
            {
                return 'Foo';
            }
        });
        $str = new Str('I am the very model of a modern major general');
        $this->assertEquals('Foo', $str->foo());
    }

    public function testCallMacroStatically()
    {
        Str::mixin(new class {
            public function foo()
            {
                return 'Foo';
            }
        });
        $str = new Str('I am the very model of a modern major general');
        $this->assertEquals('Foo', Str::foo());
    }

    public function testImplementsSerializable()
    {
        $this->assertInstanceOf('Serializable', $this->str);
    }

    public function testSerializeAndUnserialize()
    {
        $str = new Str('My string');
        $data = $str->serialize();
        $this->assertEquals(serialize('My string'), $data);
        $newStr = new Str('');
        $newStr->unserialize($data);
        $this->assertEquals($str, $newStr);
    }

    public function testDebug()
    {
        $str = new Str('My string');
        $this->assertEquals('My string', $str->__debugInfo());
    }
}
