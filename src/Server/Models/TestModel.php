<?php
/**
 * Created by PhpStorm.
 * User: tmtbe
 * Date: 16-7-15
 * Time: 下午1:44
 */

namespace Server\Models;


use Server\CoreBase\Model;
use Server\CoreBase\SwooleException;

class TestModel extends Model
{
    public function timerTest()
    {
        print_r("model timer\n");
    }

    public function test_coroutine()
    {
        $mySqlCoroutine = $this->mysql_pool->dbQueryBuilder->select('*')->from('account')->where('uid', 10303)->coroutineSend();
        $result = yield $mySqlCoroutine;
        $redisCoroutine = $this->redis_pool->coroutineSend('get', 'test');
        $result = yield $redisCoroutine;
        return $result;
    }

    public function test_coroutineII($callback)
    {
        $this->redis_pool->get('test', function ($uid) use ($callback) {
            $this->mysql_pool->dbQueryBuilder->select('*')->from('account')->where('uid', $uid);
            $this->mysql_pool->query(function ($result) use ($callback) {
                call_user_func($callback, $result);
            });
        });
    }

    public function test_exception()
    {
        throw new SwooleException('test');
    }

    public function test_exceptionII()
    {
        $result = yield $this->redis_pool->coroutineSend('get', 'test');
        $result = yield $this->mysql_pool->dbQueryBuilder->select('*')->where('uid', 10303)->coroutineSend();
    }
}