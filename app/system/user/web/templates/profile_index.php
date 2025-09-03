<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$data['page_title']=$_M['word']['memberIndex9'].$data['page_title'];
$set_user_head=str_replace($_M['url']['site'], '../', $_M['user']['head']);
?>
<include file="sys_web/head"/>
<include file="app/style"/>
<div class="met-member member-profile bg-pagebg1">
	<div class="container">
		<div class="page-content row">
			<include file='app/sidebar'/>
			<div class="col-lg-9">
				<div class="panel panel-default m-b-0">
					<div class='panel-body met-member-index met-member-profile'>
					  	<div class="panel-heading p-x-15 clearfix m-b-10">
							<h1 class="m-t-5 pull-xs-left font-size-20 m-b-0">{$word.memberIndex9}</h1>
							<a href="{$url.login_out}" class='btn btn-danger btn-outline btn-sm pull-xs-right'><i class="icon wb-power"></i> {$word.memberIndex10}</a>
						</div>
					  	<div class="basic">
							<div class="row">
								<div class="col-xs-5 col-sm-3 blue-grey-600">
									{$word.memberName}
								</div>
								<div class="col-xs-7 col-sm-9">
									{$_M['user']['username']}
								</div>
							</div>
							<div class="row">
								<div class="col-xs-5 col-sm-3 blue-grey-600">
									{$word.memberbasicType}
								</div>
								<div class="col-xs-7 col-sm-9">
									{$_M['user']['group_name']}
								</div>
							</div>
                            <if value="$_M['config']['payment_open']==1 && $data['groupshow']">
							<div class="row">
								<div class="col-xs-5 col-sm-3 blue-grey-600">
									{$word.memberbuytitle}
								</div>
								<div class="col-xs-7 col-sm-9">
									<list data="$data['groupshow']" name="$paygroup">
                                    <a href="{$url.paygroup}&a=dopaygroup&groupid={$paygroup.groupid}" class="btn btn-danger m-r-5">{$paygroup.name}</a>
                                    </list>
								</div>
							</div>
                    		</if>
							<div class="row">
								<div class="col-xs-5 col-sm-3 blue-grey-600">
									{$word.memberbasicLoginNum}
								</div>
								<div class="col-xs-7 col-sm-9">
									{$_M['user']['login_count']}
								</div>
							</div>
							<div class="row">
								<div class="col-xs-5 col-sm-3 blue-grey-600">
									{$word.memberbasicLastIP}
								</div>
								<div class="col-xs-7 col-sm-9">
									{$_M['user']['login_ip']}
								</div>
							</div>
					  	</div>
					  	<h2 class="m-t-20 font-size-20 p-x-15">{$word.memberMoreInfo}</h2>
					  	<div>
				  			<form method="post" enctype="multipart/form-data" action="{$_M['url']['info_save']}" class="para">
								<div class="met-upfile row">
									<div class="col-md-3 met-form-lable blue-grey-600">{$word.avatar}</div>
									<div class="col-md-9 form-group m-b-0">
										<input type="file" name="head" value="{$set_user_head}" data-plugin='fileinput' data-url="{$_M['url']['site']}app/system/entrance.php?lang={$_M['lang']}&c=uploadify&m=include&a=dohead" accept='image/*' hidden/>
									</div>
								</div>
								<?php if(count($_M['paralist'])){$_M['paraclass']->parawebtem($_M['user']['id'],10,0,$_M['user']['groupid'],$data['class1']);} ?>
								<div class="row m-b-0">
									<div class="col-md-3"></div>
									<div class="col-md-9">
										<button class="btn btn-primary btn-squared btn-lg p-x-50 position-sticky" type="submit">{$word.modifyinfo}</button>
									</div>
								</div>
							</form>
					  	</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<include file="sys_web/foot"/>