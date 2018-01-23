<?php

/**
 * 行为型模式
 *
 * php解析器模式
 * 理解：就是一个上下文的连接器
 * 使用场景：构建一个编译器，SQL解析器
 * 下面我们来实现一个简单增删改查的sql解析器
 *
 * @author  ALLEN_ZHAO
 * @example 运行 php test.php
 */
// 注册自加载
spl_autoload_register('autoload');

function autoload($class)
{
    require dirname($_SERVER['SCRIPT_FILENAME']) . '//..//' . str_replace('\\', '/', $class) . '.php';
}

/* * *********************************** test ************************************ */

use interpreter\SqlInterpreter;

try {
    //增加数据
    SqlInterpreter::db('user')->insert([
        'nickname' => 'test',
        'mobile' => '1366666666',
        'password' => '123456'
    ]);
    //删除数据
    SqlInterpreter::db('user')->delete([
        'nickname' => 'test',
        'mobile' => '1366666666',
    ]);
    //修改数据
    SqlInterpreter::db('member')->update([
        'id' => '1',
        'nickname' => 'testcode'
    ]);
    //查询数据
    SqlInterpreter::db('member')->find([
        'mobile' => '1366666666',
    ]);
} catch (\Exception $e) {
    echo 'error:' . $e->getMessage();
}
