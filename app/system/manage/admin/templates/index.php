<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
?>
<div class="d-flex align-items-start">
	<button type="button" class="btn btn-light rounded-10 position-sticky transition300 btn-column-control" title="{$word.manage_tips1}"><i class="fa-angle-double-left"></i></button>
	<div class="column-view-wrapper transition500 position-sticky align-self-start">
		<div class="column-view transition500 mr-4">
			<div style="padding-left: 47px;">
				<div class="position-relative">
					<div class="position-absolute" style="left: 7px;top: 50%;transform:translateY(-50%);">
						<i class="input-search-icon fa-search"></i>
					</div>
					<input type="search" name="search" placeholder="{$word.column_searchname}" data-url="{$url.own_name}c=index&a=dosearch" class="form-control pl-4 rounded-10 column-search">
				</div>
			</div>
			<div class="my-3 d-flex align-items-center justify-content-between text-nowrap">
				<div class="default_show_column-wrapper hide">
					<select name="default_show_column" class="form-control form-control-sm font-size-12"></select>
				</div>
				<div class="nav content-btn-group">
					<button type="button" href="#content-view-module" title="{$word.columnarrangement2}{$word.columnarrangement3}" data-toggle="tab" data-view_type="module" class="btn font-size-18 btn-content-module"><i class="wb-grid-4"></i></button>
					<button type="button" href="#content-view-column" title="{$word.columnarrangement2}{$word.columnarrangement4}" data-toggle="tab" data-view_type="column" class="active btn font-size-18 ml-1 btn-content-module"><i class="fa-list"></i></button>
				</div>
			</div>
			<div class="tab-content met-scrollbar scrollbar-grey">
				<div class="tab-pane fade" id="content-view-module">
					<ul class="list-group ulstyle list-group-flush column-view-list">
					</ul>
			    </div>
			    <div class="tab-pane fade active show" id="content-view-column">
			    	<ul class="list-group ulstyle list-group-flush column-view-list">
					</ul>
			    </div>
			</div>
			<div class="border-top mt-3 pt-3">
				<a href="#/recycle" class="btn-content-recycle text-content"><i class="fa-trash-o"></i> {$word.upfiletips25}</a>
			</div>
		</div>
	</div>
	<div class="content-show media-body">
		<div class="h-100 d-flex align-items-center justify-content-center hide met-loader"><div class="loader loader-round-circle"></div></div>
		<div class="h-100 content-show-item tips">
			<div class="h-100 d-flex align-items-center justify-content-center">
				<div class="h5 mb-0">{$word.admin_manage1}</div>
			</div>
		</div>
	</div>
</div>