<?php
/**
 * 简单的IOC,使Traveller类的构造函数依赖一个外部的具有visit接口的实例,手动的注入依赖
 * @Author Allen_Zhao
 * @date  2018-03-23
 */
//Ioc(Inversion of Control)模式又称依赖注入(Dependency Injection)模式.
//控制反转是将组件间的依赖关系从程序内部提到外部容器来管理,
//依赖注入是指组件的依赖通过外部以参数或其他形式,两者从本质都是一个意思就是减少模块间的耦合
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
class Traveller
{

    protected $trafficTool;

    //这里要注意,依赖注入需要通过接口来限制,而不能随意开发
    //体现了设计模式的一个原则---针对接口编程,而不是针对实现编程
    public function __construct(Visit $trafficTool)
    {
        
        $this->trafficTool = $trafficTool;
    }

    public function visitCity()
    {
        $this->trafficTool->go();
    }

}
$trafficTool = new Car();//生成依赖的交通工具实例
$tra = new Traveller($trafficTool);//依赖注入的方式解决依赖问题
$tra->visitCity();

