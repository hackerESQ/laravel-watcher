<?php

namespace HackerESQ\Watcher\Tests;

use HackerESQ\Watcher\Requests\WatcherRequest;
use Orchestra\Testbench\TestCase;

class WatcherTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // environment specific
        $app['config']->set('app.debug', env('APP_DEBUG', true));
    }

    /** @test */
    public function it_can_trigger()
    {
        $request = new WatcherRequest;

        $request->merge(['key'=>'should trigger']);

        $request->setWatcher([
            'key' => [
                'action' => fn ($context) => $context->request->merge(['test'=>'it works']),
            ]
        ]);

        $test = $request['test'];

        $this->assertEquals($test,'it works',json_encode($test));
    }

    /** @test */
    public function it_can_remove_trigger()
    {
        $request = new WatcherRequest;

        $request->merge(['key'=>'should trigger']);

        $request->setWatcher([
            'key' => [
                'action' => fn ($context) => $context->request->merge(['test'=>'it works']),
                'removeKey' => true
            ]
        ]);

        $this->assertArrayNotHasKey('key',$request->all(),json_encode($request));
    }

    /** @test */
    public function it_can_keep_trigger()
    {
        $request = new WatcherRequest;

        $request->merge(['key'=>'should trigger']);

        $request->setWatcher([
            'key' => [
                'action' => fn ($context) => $context->request->merge(['test'=>'it works']),
                'removeKey' => false
            ]
        ]);

        $this->assertArrayHasKey('key',$request->all(),json_encode($request));
    }

    /** @test */
    public function it_can_see_context()
    {
        $request = new WatcherRequest;

        $request->merge(['key'=>'should trigger']);

        $request->setWatcher([
            'key' => [
                'action' => fn ($context) => $context->request->merge(['test'=>$context->trigger]),
            ]
        ]);

        $this->assertEquals('key',$request['test'],json_encode($request));
    }

    /** @test */
    public function it_can_trigger_multiple()
    {
        $request = new WatcherRequest;

        $request->merge([
            'foo'=>'should trigger',
            'bar'=>'should trigger too',
            'baz'=>'should not trigger'
        ]);

        $request->setWatcher([
            'foo' => [
                'action' => fn ($context) => $context->request->merge(['foo_worked'=>true]),
            ],
            'bar' => [
                'action' => fn ($context) => $context->request->merge(['bar_worked'=>true]),
            ]
        ]);

        $this->assertTrue($request['foo_worked'],json_encode($request));
        $this->assertTrue($request['bar_worked'],json_encode($request));

        $test = [
            'foo'=>'should trigger',
            'foo_worked' => true,
            'bar'=>'should trigger too',
            'bar_worked' => true,
            'baz'=>'should not trigger',
        ];

        $this->assertEquals($test,$request->all(),json_encode($request));
    }
}