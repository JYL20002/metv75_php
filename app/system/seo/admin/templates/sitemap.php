<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
?>

<div class="met-seo">
  <form method="POST" action="{$url.own_name}c=map&a=doSaveSiteMap" class="info-form" data-submit-ajax="1">
    <div class="metadmin-fmbx">
      <h3 class="example-title">{$word.unitytxt_1}</h3>
      <dl>
        <dt>
          <label class="form-control-label">{$word.setimgWater}</label>
        </dt>
        <dd>
          <div class="form-group clearfix">
            <input type="checkbox" data-plugin="switchery" name="met_sitemap_auto" value="0"/>
            <span class="text-help ml-2">{$word.unitytxt_77}</span>
            <a href="https://help.metinfo.cn/faq/398.html" target="_blank" class='ml-2'>{$word.seotips14_1}</a>
          </div>
        </dd>
      </dl>
      <dl>
        <dt>
          <label class="form-control-label">{$word.seotips16}</label>
        </dt>
        <dd>
          <div class="form-group clearfix">
            <div class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" name="met_sitemap_not1" value="1" class="custom-control-input" id="met_sitemap_not1" />
              <label class="custom-control-label" for="met_sitemap_not1">{$word.seotipssitemap1}</label>
            </div>
            <div class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" name="met_sitemap_not2" value="1" class="custom-control-input" id="met_sitemap_not2" />
              <label class="custom-control-label" for="met_sitemap_not2">{$word.seotips18}</label>
            </div>
            <div class="text-help d-block">{$word.seotips2}</div>
          </div>
        </dd>
      </dl>
      <dl>
        <dt>
          <label class="form-control-label">{$word.seotips19}</label>
        </dt>
        <dd>
          <div class="form-group clearfix">
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" name="met_sitemap_lang" value="1" class="custom-control-input" id="met_sitemap_lang-1" />
              <label class="custom-control-label" for="met_sitemap_lang-1">{$word.admintips1}</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" name="met_sitemap_lang" value="0" class="custom-control-input" id="met_sitemap_lang-0" />
              <label class="custom-control-label" for="met_sitemap_lang-0">{$word.seotips20}</label>
            </div>
            <span class="text-help ml-2">{$word.seotips21}</span>
          </div>
        </dd>
      </dl>
      <dl>
        <dt>
          <label class="form-control-label">Sitemap{$word.type}</label>
        </dt>
        <dd>
          <div class="form-group clearfix">
            <div class="d-flex">
              <div class="mr-3">
                <div class="custom-control custom-checkbox ">
                  <input type="checkbox" name="met_sitemap_xml" value="1" class="custom-control-input" id="met_sitemap_xml-1" />
                  <label class="custom-control-label" for="met_sitemap_xml-1">{$word.sethtmsitemap4}</label>
                </div>
                <div class="text-help ml-4">{$word.seotips15_2}{$word.seotips15} <a href="" target="_blank" class="met-sitemap-xml" data-type="xml" data-met_index_type="{$c.met_index_type}"></a></div>
              </div>
              <div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" name="met_sitemap_txt" value="1" class="custom-control-input" id="met_sitemap_txt-1" />
                  <label class="custom-control-label" for="met_sitemap_txt-1">Txt{$word.mod12}</label>
                </div>
                <div class="text-help ml-4">{$word.seotips15_3}{$word.seotips15} <a href="" target="_blank" class="met-sitemap-txt" data-type="txt" data-met_index_type="{$c.met_index_type}"></a></div>
              </div>
            </div>
          </div>
        </dd>
      </dl>
      <dl>
        <dt></dt>
        <dd>
          <button type="submit" class="btn btn-primary px-4">{$word.save}</button>
          <button type="submit" data-url="{$url.own_name}c=map&a=doCreateSiteMap" class="btn btn-default ml-2 btn-update-sitemap">{$word.update}sitemap</button>
          <button type="submit" data-url="{$url.own_name}c=map&a=doCreateRobots" class="btn btn-default ml-2 btn-update-robots">{$word.update}robots</button>
        </dd>
      </dl>
    </div>
  </form>
</div>
