<?php

/**
 * 抢红包金额分配
 * @Author Allen_Zhao
 * @date  2018-03-05
 */
/* * 生成红包的函数 */

function getRandMoney($totalMoney, $totalPeople = 2, $miniMoney = 1)
{
    $randRemainMoney = $totalMoney - $totalPeople * $miniMoney; //剩余需要随机的钱数
    return _getRandMoney($randRemainMoney, $totalPeople, $miniMoney);
}

/* * 红包生成的逻辑代码 */

function _getRandMoney($totalMoney, $totalPeople, $miniMoney)
{
    $returnMessage = array('status' => 1, 'data' => NULL);
    if ($totalMoney > 0) {
        $returnMessage['data'] = _randMoney($totalMoney, $totalPeople, $miniMoney);
    } elseif ($totalMoney == 0) {
        $returnMessage['data'] = array_fill(0, $totalPeople, 1);
    } else {
        $returnMessage['status'] = -1;
        $returnMessage['data'] = '参数传递有误，生成红包失败';
    }
    return $returnMessage;
}

/* 参数无误，开始生成对应的红包金额 */

function _randMoney($totalMoney, $totalPeople, $miniMoney)
{
    $data = array_fill(0, $totalPeople, $miniMoney);
    if ($totalPeople > 1) {
        foreach ($data as $k => $v) {
            if ($k == $totalPeople - 1) {
                $data[$k] = $totalMoney + $v;
                break;
            } else {
                if ($totalMoney == 0)
                    break;
                $randMoney = rand(0, $totalMoney);
                $totalMoney -= $randMoney;
                $data[$k] = $randMoney + $v;
            }
        }
    }
    return $data;
}

class redpack
{

//总金额
    private $total = 0;
//红包数量
    private $num = 0;
//最小红包金额
    private $min = 0.01;

    public function __construct($total, $num, $min)
    {
        $this->total = $total;
        $this->num = $num;
        $this->min = $min;
    }

//红包结果
    public function getPack()
    {
        $total = $this->total;
        $num = $this->num;
        $min = $this->min;
        for ($i = 1; $i < $num; $i++) {
            $safe_total = ($total - ($num - $i) * $min) / ($num - $i); //随机安全上限
            $money = mt_rand($min * 100, $safe_total * 100) / 100;
            $total = $total - $money;
//红包数据
            $readPack[] = [
                'money' => $money,
                'balance' => $total,
            ];
        }
//最后一个红包，不用随机
        $readPack[] = [
            'money' => $money,
            'balance' => 0,
        ];
//返回结果
        return $readPack;
    }

}

$money = getRandMoney(20, 10, 0.01);
var_dump($money);
$total = 20; //红包总金额
$num = 10; // 分成10个红包，支持10人随机领取
$min = 0.01; //每个人最少能收到0.01元
$redpack = new redpack($total, $num, $min);
$jieguo = $redpack->getPack();
foreach ($jieguo as $key => $val) {
    $n = $key + 1;
    echo '第' . $n . '个红包：' . $val['money'] . ' 元，余额：' . $val['balance'] . ' 元<br>';
}
