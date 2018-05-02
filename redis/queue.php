<?php

/**
 * redis实战
 * Redis practice
 *
 * 利用列表list实现简单队列
 * redis的队列为双向链表(通过lpush,lpop,rpush,rpop等方法)实现两头都可以修改队列
 * Use list to implement a simple queue
 *
 * @author ALLEN_ZHAO
 * @date 2018-01-02
 */
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

// 从尾部进队列
// push data to queue
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME', json_encode(['user_id' => $userId]));
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME', json_encode(['user_id' => $userId]));
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME', json_encode(['user_id' => $userId]));
echo "数据进队列成功<br/>";
echo "push data to queue success<br/>";

// 查看队列
// show queue
$res = $redis->lrange('QUEUE_NAME', 0, -1);
echo "当前队列数据为：<br/>";
echo "The queue's data are：<br/>";
print_r($res);

echo "<br/>-----------------------------<br/>";

// 从头部出队列
// pop up the earlier data from queue
$redis->lpop('QUEUE_NAME');
echo "数据出队列成功<br/>";
echo "pop up success<br/>";

// 查看队列
$res = $redis->lrange('QUEUE_NAME', 0, -1);
echo "当前队列数据为：<br/>";
echo "The queue's data are：<br/>";
print_r($res);
