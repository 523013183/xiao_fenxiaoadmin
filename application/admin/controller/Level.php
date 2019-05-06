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
namespace app\admin\controller;

use app\service\LevelService;

/**
 * 分销层级管理
 */
class Level extends Common
{
	/**
	 * 构造方法
	 */
	public function __construct()
	{
		// 调用父类前置方法
		parent::__construct();
	}

	/**
     * [Index 管理员列表]
     */
	public function Index()
	{
		// 登录校验
		$this->IsLogin();
		
		// 权限校验
		$this->IsPower();

		// 参数
		$params = input();
        $params['admin_id'] = $this->admin['id'];
		// 条件
		$where = LevelService::LevelListWhere($params);

		// 总数
		$total = LevelService::LevelTotal($where);

		// 分页
		$number = MyC('admin_page_number', 10, true);
		$page_params = array(
				'number'	=>	$number,
				'total'		=>	$total,
				'where'		=>	$params,
				'page'		=>	isset($params['page']) ? intval($params['page']) : 1,
				'url'		=>	MyUrl('admin/level/index'),
			);
		$page = new \base\Page($page_params);

		// 获取管理员列表
		$data_params = [
			'where'		=> $where,
			'm'			=> $page->GetPageStarNumber(),
			'n'			=> $number,
		];
		$data = LevelService::LevelList($data_params);

		$this->assign('params', $params);
		$this->assign('page_html', $page->GetPageHtml());
		$this->assign('data', $data);
		return $this->fetch();
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

		// 管理员编辑
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
				return $this->error('信息不存在', MyUrl('admin/index/index'));
			}
			$data = $ret[0];
		}
		$this->assign('data', $data);

		$this->assign('id', isset($params['id']) ? $params['id'] : 0);
		return $this->fetch();
	}

	/**
     * [Save 添加/编辑]
     */
	public function Save()
	{
		// 是否ajax
		if(!IS_AJAX)
		{
			return $this->error('非法访问');
		}

		// 登录校验
		$this->IsLogin();

		// 参数
		$params = input('post.');

		// 不是操作自己的情况下
		if(!isset($params['id']) || $params['id'] != $this->admin['id'])
		{
			// 权限校验
			$this->IsPower();
		}
		$params['admin_id'] = $this->admin['id'];
 		return LevelService::save($params);
	}

	/**
	 * [Delete 删除]
	 */
	public function Delete()
	{
		// 是否ajax
		if(!IS_AJAX)
		{
			return $this->error('非法访问');
		}

		// 登录校验
		$this->IsLogin();

		// 权限校验
		$this->IsPower();

		// 开始操作
		$params = input('post.');
		$params['admin'] = $this->admin;
		return LevelService::delete($params);
	}
}
?>