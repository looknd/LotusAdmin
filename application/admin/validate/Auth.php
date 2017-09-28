<?php
namespace app\admin\validate;

use think\Validate;

class Auth extends Validate
{
    protected $rule = [
        'pid'       => 'require',
        'title'     => 'require|min:2|max:15|unique:auth_rule',
        'name'      => 'require|unique:auth_rule',
        'sort'      =>'require|number',
    ];
    protected $message = [
    	'title.require' =>'菜单名称不能为空',
    	'title.min'		=>'菜单名称太短',
    	'title.max'		=>'菜单名称太长',
    	'title.unique'	=>'系统中已经存在该菜单名称',
    	'name.require'	=>'控制器不能为空',
    	'name.unique'	=>'控制器必须唯一',
        'sort.require'  =>'排序不能为空',
        'sort.number'   =>'排序必须为数字',     
    ];
    protected $scene = [
        'edit'  => [
            'title',
            'name',
            'sort',
        ],
    ];

}
