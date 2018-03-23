<?php

/**
 * 通过工厂模式解决旅行者与交通工具的依赖关系
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

//工厂模式
class TrafficToolFactory
{

    public function createTrafficTool($name)
    {
        //虽然解决了旅行者与交通工具依赖关系,但是交通工具工厂与旅行者产生了依赖
        //如果需求增加时,我们需要修改简单工厂模式,如果依赖增多,工厂将十分庞大,依然不好维护.所以引出IOC模式
        switch ($name) {
            case 'Leg':
                return new Leg();

                break;
            case 'Car':
                return new Car();

                break;
            case 'Train':
                return new Train();

                break;
            default:
                exit("set trafficTool error!");
                break;
        }
    }

}

class Traveller
{

    protected $trafficTool;

    public function __construct($trafficTool)
    {
        //通过工厂产生依赖的交通工具实例
        $factory = new TrafficToolFactory();
        $this->trafficTool = $factory->createTrafficTool($trafficTool);
    }

    public function visitCity()
    {
        $this->trafficTool->go();
    }

}

$tra = new Traveller('Train');
$tra->visitCity();
