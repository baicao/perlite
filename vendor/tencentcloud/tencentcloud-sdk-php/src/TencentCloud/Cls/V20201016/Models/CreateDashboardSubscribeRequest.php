<?php
/*
 * Copyright (c) 2017-2018 THL A29 Limited, a Tencent company. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TencentCloud\Cls\V20201016\Models;
use TencentCloud\Common\AbstractModel;

/**
 * CreateDashboardSubscribe请求参数结构体
 *
 * @method string getName() 获取仪表盘订阅名称。
 * @method void setName(string $Name) 设置仪表盘订阅名称。
 * @method string getDashboardId() 获取仪表盘id。
 * @method void setDashboardId(string $DashboardId) 设置仪表盘id。
 * @method string getCron() 获取订阅时间cron表达式，格式为：{秒数} {分钟} {小时} {日期} {月份} {星期}；（有效数据为：{分钟} {小时} {日期} {月份} {星期}）。<br><li/>{秒数} 取值范围： 0 ~ 59 <br><li/>{分钟} 取值范围： 0 ~ 59  <br><li/>{小时} 取值范围： 0 ~ 23  <br><li/>{日期} 取值范围： 1 ~ 31 AND (dayOfMonth最后一天： L) <br><li/>{月份} 取值范围： 1 ~ 12 <br><li/>{星期} 取值范围： 0 ~ 6 【0:星期日， 6星期六】
 * @method void setCron(string $Cron) 设置订阅时间cron表达式，格式为：{秒数} {分钟} {小时} {日期} {月份} {星期}；（有效数据为：{分钟} {小时} {日期} {月份} {星期}）。<br><li/>{秒数} 取值范围： 0 ~ 59 <br><li/>{分钟} 取值范围： 0 ~ 59  <br><li/>{小时} 取值范围： 0 ~ 23  <br><li/>{日期} 取值范围： 1 ~ 31 AND (dayOfMonth最后一天： L) <br><li/>{月份} 取值范围： 1 ~ 12 <br><li/>{星期} 取值范围： 0 ~ 6 【0:星期日， 6星期六】
 * @method DashboardSubscribeData getSubscribeData() 获取仪表盘订阅数据。
 * @method void setSubscribeData(DashboardSubscribeData $SubscribeData) 设置仪表盘订阅数据。
 */
class CreateDashboardSubscribeRequest extends AbstractModel
{
    /**
     * @var string 仪表盘订阅名称。
     */
    public $Name;

    /**
     * @var string 仪表盘id。
     */
    public $DashboardId;

    /**
     * @var string 订阅时间cron表达式，格式为：{秒数} {分钟} {小时} {日期} {月份} {星期}；（有效数据为：{分钟} {小时} {日期} {月份} {星期}）。<br><li/>{秒数} 取值范围： 0 ~ 59 <br><li/>{分钟} 取值范围： 0 ~ 59  <br><li/>{小时} 取值范围： 0 ~ 23  <br><li/>{日期} 取值范围： 1 ~ 31 AND (dayOfMonth最后一天： L) <br><li/>{月份} 取值范围： 1 ~ 12 <br><li/>{星期} 取值范围： 0 ~ 6 【0:星期日， 6星期六】
     */
    public $Cron;

    /**
     * @var DashboardSubscribeData 仪表盘订阅数据。
     */
    public $SubscribeData;

    /**
     * @param string $Name 仪表盘订阅名称。
     * @param string $DashboardId 仪表盘id。
     * @param string $Cron 订阅时间cron表达式，格式为：{秒数} {分钟} {小时} {日期} {月份} {星期}；（有效数据为：{分钟} {小时} {日期} {月份} {星期}）。<br><li/>{秒数} 取值范围： 0 ~ 59 <br><li/>{分钟} 取值范围： 0 ~ 59  <br><li/>{小时} 取值范围： 0 ~ 23  <br><li/>{日期} 取值范围： 1 ~ 31 AND (dayOfMonth最后一天： L) <br><li/>{月份} 取值范围： 1 ~ 12 <br><li/>{星期} 取值范围： 0 ~ 6 【0:星期日， 6星期六】
     * @param DashboardSubscribeData $SubscribeData 仪表盘订阅数据。
     */
    function __construct()
    {

    }

    /**
     * For internal only. DO NOT USE IT.
     */
    public function deserialize($param)
    {
        if ($param === null) {
            return;
        }
        if (array_key_exists("Name",$param) and $param["Name"] !== null) {
            $this->Name = $param["Name"];
        }

        if (array_key_exists("DashboardId",$param) and $param["DashboardId"] !== null) {
            $this->DashboardId = $param["DashboardId"];
        }

        if (array_key_exists("Cron",$param) and $param["Cron"] !== null) {
            $this->Cron = $param["Cron"];
        }

        if (array_key_exists("SubscribeData",$param) and $param["SubscribeData"] !== null) {
            $this->SubscribeData = new DashboardSubscribeData();
            $this->SubscribeData->deserialize($param["SubscribeData"]);
        }
    }
}