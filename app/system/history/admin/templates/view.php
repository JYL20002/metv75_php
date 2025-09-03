<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$handle=$data['handle'];
$data['n']=$data['mod'];
?>
<div class="metadmin-fmbx">
    <h3 class='example-title'>{$word.upfiletips7}</h3>
    <if value="$data['n'] neq 'job'">
        <dl>
            <dt>
                <label class='form-control-label'>
                    <if value="$data['n'] eq 'news'">{$word.articletitle}<elseif value="$data['n'] eq 'product'"/>{$word.titletips}<else/>{$word.title}</if>
                </label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.title}</div>
            </dd>
        </dl>
        <if value="in_array($data['n'],array('product','news','img','download'))">
        <dl>
            <dt>
                <label class='form-control-label'>
                    <if value="in_array($data['n'],array('product','img'))">{$word.displayimg}<else/>{$word.coverimg}</if>
                </label>
            </dt>
            <dd>
                <if value="in_array($data['n'],array('product','img'))">
                <div class='d-flex flex-wrap'>
                    <?php
                    $handle['imgurl_all']=explode('|',$handle['imgurl_all']);
                    foreach ($handle['imgurl_all'] as $key => $value) {
                    ?>
                    <a href="{$value}" title="{$word.clickview}" target="_blank" class='mr-2 mb-2'><img src="{$value}" class='img-fluid' style="max-height: 100px;"></a>
                    <?php } ?>
                </div>
                <div class='met-editor'>{$handle.video}</div>
                <else/>
                <if value="$handle['imgurl']">
                <a href="{$handle.imgurl}" title="{$word.clickview}" target="_blank"><img src="{$handle.imgurl}" class='img-fluid' style="max-height: 100px;"></a>
                </if>
                </if>
            </dd>
        </dl>
        </if>
        <if value="$data['n'] eq 'download'">
        <h3 class='example-title'>{$word.unitytxt_34}</h3>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.downloadurl}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>
                    <a href="{$handle.downloadurl}" title="{$word.clickview}" target="_blank">{$handle.downloadurl}</a>
                </div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.setfilesize}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.filesize}</div>
            </dd>
        </dl>
        </if>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.contentdetail}</label>
            </dt>
            <dd>
                <if value="$data['n'] eq 'product'">
                <?php
                $checkbox_time=time();
                for ($i = 1; $i < 5; $i++) {
                    $product_content[]=array(
                        'value'=>$handle['content'.$i]
                    );
                }
                ?>
                <div class="nav nav-underline position-relative">
                    <a class="nav-link active" data-toggle="tab" href="#product-content-{$checkbox_time}">{$word.contentdetail}1</a>
                    <list data="$product_content" name="$v">
                    <?php $v['sort']=$v['_index']+1; ?>
                    <a class="nav-link" data-toggle="tab" href="#product-content{$v.sort}-{$checkbox_time}">{$word.contentdetail}{$v.sort}</a>
                    </list>
                </div>
                <div class="tab-content mt-2 product-details-content">
                    <div class="tab-pane fade show active met-editor" id="product-content-{$checkbox_time}">{$handle.content}</div>
                    <list data="$product_content" name="$v">
                    <?php $v['sort']=$v['_index']+1; ?>
                    <div class="tab-pane fade met-editor" id="product-content{$v.sort}-{$checkbox_time}">{$v.value}</div>
                    </list>
                </div>
                <else/>
                <div class="met-editor">{$handle.content}</div>
                </if>
            </dd>
        </dl>
        <h3 class='example-title clearfix'><span class="my-1 d-inline-block">SEO{$word.seting}</span></h3>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.managertyp5}{$word.columnmtitle}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.ctitle}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.keywords}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.keywords}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.desctext}</label>
            </dt>
            <dd class="clearfix">
                <div class='text-help text-dark'>{$handle.description}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.tag}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.tag}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.columnhtmlname}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.filename}</div>
            </dd>
        </dl>
        <h3 class='example-title clearfix'><span class="my-1 d-inline-block">{$word.unitytxt_15}</span></h3>
        <if value="$data['n'] eq 'news'">
        <dl>
            <dt>
                <label class='form-control-label'>{$word.modpublish}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.publisher}</div>
            </dd>
        </dl>
        </if>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.js79}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.hits}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.linkto}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.links}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.setfootOther}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.other_info}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.custom_info}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.custom_info}</div>
            </dd>
        </dl>
    <else/>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.jobposition}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.position}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.jobaddress}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.place}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.jobdeal}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.deal}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.jobnum}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.count}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.cvemail}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.email}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.joblife}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.useful_life}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.columnhtmlname}</label>
            </dt>
            <dd>
                <div class='text-help text-dark'>{$handle.filename}</div>
            </dd>
        </dl>
        <dl>
            <dt>
                <label class='form-control-label'>{$word.contentdetail}</label>
            </dt>
            <dd>
                <div class="met-editor">{$handle.content}</div>
            </dd>
        </dl>
    </if>
	<dl>
        <dt>
            <label class='form-control-label'>{$word.smstips64}{$displaytype.marks}</label>
        </dt>
        <dd>
            <div class='text-help text-dark'>
                <if value="$handle['displaytype']">
                <span class='mr-3'>{$word.displaytype}</span>
                </if>
                <if value="$handle['com_ok']">
                <span class='mr-3'>{$word.recom}</span>
                </if>
                <if value="$handle['top_ok']">
                <span class='mr-3'>{$word.top}</span>
                </if>
            </div>
        </dd>
    </dl>
	<dl>
		<dt>
			<label class='form-control-label'>{$word.updatetime}</label>
		</dt>
		<dd>
            <div class='text-help text-dark'>{$handle.updatetime}</div>
		</dd>
	</dl>
	<dl>
		<dt>
			<label class='form-control-label'>{$word.addtime}</label>
		</dt>
		<dd>
            <div class='text-help text-dark'>{$handle.addtime}</div>
		</dd>
	</dl>
</div>