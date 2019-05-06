<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://shopxo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\service;

use app\admin\controller\Level;
use think\Db;

/**
 * 服务层
 */
class LevelService
{
    /**
     * 层级列表
     * @param    [array]          $params [输入参数]
     */
    public static function LevelList($params = [])
    {
        $where = empty($params['where']) ? [] : $params['where'];
        $field = empty($params['field']) ? '*' : $params['field'];
        $order_by = empty($params['order_by']) ? 'id desc' : trim($params['order_by']);

        $m = isset($params['m']) ? intval($params['m']) : 0;
        $n = isset($params['n']) ? intval($params['n']) : 10;

        // 获取管理员列表
        $data = Db::name('Level')->where($where)->field($field)->order($order_by)->limit($m, $n)->select();
        return $data;
    }

    /**
     * 列表条件
     * @param    [array]          $params [输入参数]
     */
    public static function LevelListWhere($params = [])
    {
        $where = [];
        if(!empty($params['name']))
        {
            $where[] = ['name', 'like', '%'.$params['name'].'%'];
        }
        $where[] = ['admin_id', '=', $params['admin_id']];

        return $where;
    }

    /**
     * 总数
     * @param    [array]          $where [条件]
     */
    public static function LevelTotal($where)
    {
        return (int) Db::name('Level')->where($where)->count();
    }

    /**
     * [SaveInfo 添加/编辑页面]
     */
    public function SaveInfo()
    {
        // 登录校验
        $this->IsLogin();

        // 参数
        $params = input();

        // 不是操作自己的情况下
        if(!isset($params['id']) || $params['id'] != $this->admin['id'])
        {
            // 权限校验
            $this->IsPower();
        }

        // 编辑
        $data = [];
        if(!empty($params['id']))
        {
            $data_params = [
                'where'		=> ['id'=>$params['id']],
                'm'			=> 0,
                'n'			=> 1,
            ];
            $ret = LevelService::LevelList($data_params);
            if(empty($ret[0]))
            {
                return $this->error('信息不存在', MyUrl('admin/level/index'));
            }
            $data = $ret[0];
        }
        $this->assign('data', $data);
        $this->assign('id', isset($params['id']) ? $params['id'] : 0);
        return $this->fetch();
    }

    /**
     * 保存
     * @param    [array]          $params [输入参数]
     */
    public static function save($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'name',
                'error_msg'         => '名称不能为空',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'number',
                'error_msg'         => '标识不能为空',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'level',
                'error_msg'         => '级别不正确',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }
        return empty($params['id']) ? self::LevelInsert($params) : self::LevelUpdate($params);
    }

    /**
     * 添加
     * @param    [array]          $params [输入参数]
     */
    public static function LevelInsert($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'name',
                'error_msg'         => '名称不能为空',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'number',
                'error_msg'         => '标识不能为空',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'level',
                'error_msg'         => '级别不正确',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        $data = [
            'name'      => $params['name'],
            'number'     => $params['number'],
            'level'        => intval($params['level']),
            'add_time'      => time(),
            'admin_id' => $params['admin_id']
        ];

        // 添加
        if(Db::name('Level')->insert($data) > 0)
        {
            return DataReturn('新增成功', 0);
        }
        return DataReturn('新增失败', -100);
    }

    /**
     * 更新
     * @param    [array]          $params [输入参数]
     */
    public static function LevelUpdate($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'name',
                'error_msg'         => '名称不能为空',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'number',
                'error_msg'         => '标识不能为空',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'level',
                'error_msg'         => '级别不正确',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }
        // 数据
        $data = [
            'name'      => $params['name'],
            'number'     => $params['number'],
            'level'        => intval($params['level']),
            'upd_time'      => time(),
        ];

        // 更新
        if(Db::name('Level')->where(['id'=>intval($params['id'])])->update($data))
        {
            return DataReturn('编辑成功', 0);
        }
        return DataReturn('编辑失败或数据未改变', -100);
    }

    /**
     * 删除
     * @param    [array]          $params [输入参数]
     */
    public static function delete($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'id',
                'error_msg'         => '删除id有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }
           
        // 删除操作
        if(Db::name('Level')->delete(intval($params['id'])))
        {
            return DataReturn('删除成功');
        }
        return DataReturn('删除失败或资源不存在', -100);
    }
}
?>