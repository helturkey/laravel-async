<?php
/**
 * @link https://github.com/vuongxuongminh/laravel-async
 *
 * @copyright (c) Vuong Xuong Minh
 * @license [MIT](https://opensource.org/licenses/MIT)
 */

namespace VXM\Async\Tests;

use Async;

/**
 * @author Vuong Minh <vuongxuongminh@gmail.com>
 * @since 1.0.0
 */
class JobTest extends TestCase
{
    /**
     * @dataProvider successJobProvider
     */
    public function testHandleSuccess($handler, array $events)
    {
        Async::run($handler, $events);

        $this->assertStringContainsString('ok!', current(Async::wait()));
    }

    public function testBatchHandleSuccess()
    {
        Async::batchRun(...$this->successJobProvider());

        foreach (Async::wait() as $result) {
            $this->assertStringContainsString('ok!', $result);
        }
    }

    /**
     * @dataProvider errorJobProvider
     */
    public function testHandleError($handler, array $events)
    {
        Async::run($handler, $events);
        $this->assertEmpty(Async::wait());
    }

    public function testBatchHandleError()
    {
        Async::batchRun(...$this->errorJobProvider());
        $this->assertEmpty(Async::wait());
    }

    public function successJobProvider(): array
    {
        return [
            [
                TestClass::class,
                [
                    'success' => 'VXM\Async\Tests\EventTestClass@success',
                ],
            ],
            [
                new TestClass,
                [
                    'success' => 'VXM\Async\Tests\EventTestClass@success',
                ],
            ],
            [
                function () {
                    return 'ok!';
                },
                [
                    'success' => 'VXM\Async\Tests\EventTestClass@success',
                ],
            ],
        ];
    }

    public function errorJobProvider(): array
    {
        return [
            [
                TestClass::class.'@handleException',
                [
                    'error' => 'VXM\Async\Tests\EventTestClass@catch',
                ],
            ],
            [
                function () {
                    throw new TestException('ok!');
                },
                [
                    'error' => 'VXM\Async\Tests\EventTestClass@catch',
                ],
            ],
        ];
    }
}
