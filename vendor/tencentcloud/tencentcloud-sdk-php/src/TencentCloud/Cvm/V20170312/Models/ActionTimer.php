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
namespace TencentCloud\Cvm\V20170312\Models;
use TencentCloud\Common\AbstractModel;

/**
 * 定时任务
 *
 * @method string getTimerAction() 获取定时器动作，目前仅支持销毁一个值：TerminateInstances。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setTimerAction(string $TimerAction) 设置定时器动作，目前仅支持销毁一个值：TerminateInstances。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getActionTime() 获取执行时间，按照ISO8601标准表示，并且使用UTC时间。格式为 YYYY-MM-DDThh:mm:ssZ。例如 2018-05-29T11:26:40Z，执行时间必须大于当前时间5分钟。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setActionTime(string $ActionTime) 设置执行时间，按照ISO8601标准表示，并且使用UTC时间。格式为 YYYY-MM-DDThh:mm:ssZ。例如 2018-05-29T11:26:40Z，执行时间必须大于当前时间5分钟。
注意：此字段可能返回 null，表示取不到有效值。
 * @method Externals getExternals() 获取扩展数据
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setExternals(Externals $Externals) 设置扩展数据
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getActionTimerId() 获取定时器ID。
 * @method void setActionTimerId(string $ActionTimerId) 设置定时器ID。
 * @method string getStatus() 获取定时器状态，取值范围：

UNDO：未触发
DOING：触发中
DONE：已经触发

 * @method void setStatus(string $Status) 设置定时器状态，取值范围：

UNDO：未触发
DOING：触发中
DONE：已经触发

 * @method string getInstanceId() 获取定时器对应的实例ID。
 * @method void setInstanceId(string $InstanceId) 设置定时器对应的实例ID。
 */
class ActionTimer extends AbstractModel
{
    /**
     * @var string 定时器动作，目前仅支持销毁一个值：TerminateInstances。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $TimerAction;

    /**
     * @var string 执行时间，按照ISO8601标准表示，并且使用UTC时间。格式为 YYYY-MM-DDThh:mm:ssZ。例如 2018-05-29T11:26:40Z，执行时间必须大于当前时间5分钟。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $ActionTime;

    /**
     * @var Externals 扩展数据
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $Externals;

    /**
     * @var string 定时器ID。
     */
    public $ActionTimerId;

    /**
     * @var string 定时器状态，取值范围：

UNDO：未触发
DOING：触发中
DONE：已经触发

     */
    public $Status;

    /**
     * @var string 定时器对应的实例ID。
     */
    public $InstanceId;

    /**
     * @param string $TimerAction 定时器动作，目前仅支持销毁一个值：TerminateInstances。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $ActionTime 执行时间，按照ISO8601标准表示，并且使用UTC时间。格式为 YYYY-MM-DDThh:mm:ssZ。例如 2018-05-29T11:26:40Z，执行时间必须大于当前时间5分钟。
注意：此字段可能返回 null，表示取不到有效值。
     * @param Externals $Externals 扩展数据
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $ActionTimerId 定时器ID。
     * @param string $Status 定时器状态，取值范围：

UNDO：未触发
DOING：触发中
DONE：已经触发

     * @param string $InstanceId 定时器对应的实例ID。
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
        if (array_key_exists("TimerAction",$param) and $param["TimerAction"] !== null) {
            $this->TimerAction = $param["TimerAction"];
        }

        if (array_key_exists("ActionTime",$param) and $param["ActionTime"] !== null) {
            $this->ActionTime = $param["ActionTime"];
        }

        if (array_key_exists("Externals",$param) and $param["Externals"] !== null) {
            $this->Externals = new Externals();
            $this->Externals->deserialize($param["Externals"]);
        }

        if (array_key_exists("ActionTimerId",$param) and $param["ActionTimerId"] !== null) {
            $this->ActionTimerId = $param["ActionTimerId"];
        }

        if (array_key_exists("Status",$param) and $param["Status"] !== null) {
            $this->Status = $param["Status"];
        }

        if (array_key_exists("InstanceId",$param) and $param["InstanceId"] !== null) {
            $this->InstanceId = $param["InstanceId"];
        }
    }
}