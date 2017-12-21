<?php 
namespace app\admin\model;
use think\Model;

class Orders extends Model
{
    public function getStatusAttr($value){
        
        return $status[$value];
    }
}
