<?php

/**
 * IOC核心,通过php反射机制自动的注入依赖
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

//设计容器类,容器类装实例或提供实例的回调函数
class Container
{

    //用于装提供实例的回调函数,真正的容器还会装实例等其他内容
    //从而实现单例等高级功能
    protected $bindings = [];

    //绑定接口和生成相应实例的回调函数
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (!$concrete instanceof Closure) {
            //如果提供的参数不是回调函数,则产生默认的回调函数
            $concrete = $this->getClosure($abstract, $concrete);
        }
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    //默认生成实例的回调函数
    protected function getClosure($abstract, $concrete)
    {
        //生成实例的回调函数,$c一般为IoC容器对象,在调用回调生成实例时提供
        //即build函数中的$concrete($this)
        return function($c) use ($abstract, $concrete) {
            $method = ($abstract == $concrete) ? 'build' : 'make';
            //调用的是容器的build或make方法生成实例
            return $c->$method($concrete);
        };
    }

    //生成实例对象,首先解决接口和实例化类之间的依赖关系
    public function make($abstract)
    {
        $concrete = $this->getConcrete($abstract);
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }
        return $object;
    }

    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    //获取绑定的回调函数
    public function getConcrete($abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            return $abstract;
        }
        return $this->bindings[$abstract]['concrete'];
    }

    //实例化对象
    public function build($concrete)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }
        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            echo $message = "Target [$concrete] is not instantiable";
        }
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete;
        }
        $dependencies = $constructor->getParameters();
        $instances = $this->getDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    //解决通过反射机制实例化对象时的依赖
    protected function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (is_null($dependency)) {
                $dependencies[] = NULL;
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }
        return (array) $dependencies;
    }

    protected function resolveClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
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

//实例化IOC容器
$app = new Container();
//完成容器的填充
$app->bind("Visit", "Train");
$app->bind("traveller", "Traveller");
//通过容器实现依赖注入,完成类的实例化
$tra = $app->make("traveller");
$tra->visitCity();
