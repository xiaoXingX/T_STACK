<?php

/**
 * 依赖关系
 * @Author Allen_Zhao
 * @date  2018-03-23
 */
interface visit
{

    public function go();
}

//实现不同交通工具
class Leg implements visit
{

    public function go()
    {
        echo "walk to City";
    }

}

class Car implements visit
{

    public function go()
    {
        echo "drive to City";
    }

}

class Train implements visit
{

    public function go()
    {
        echo "train to City";
    }

}

//设计旅行者类,该类在实现游览大城市的功能时要依赖交通工具实例
class Traveller
{

    protected $trafficTool;

    public function __construct()
    {
        //依赖产生
        //如果需要换用其它的交通工具,则每次都需要改类的内部
        //要想解决依赖问题,就需要利用IOC思想
        $this->trafficTool = new Leg();
    }

    public function visitCity()
    {
        $this->trafficTool->go();
    }

}

$tra = new Traveller();

$tra->visitCity();
