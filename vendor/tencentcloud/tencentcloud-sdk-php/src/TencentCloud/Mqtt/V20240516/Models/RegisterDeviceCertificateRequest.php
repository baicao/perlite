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
namespace TencentCloud\Mqtt\V20240516\Models;
use TencentCloud\Common\AbstractModel;

/**
 * RegisterDeviceCertificate请求参数结构体
 *
 * @method string getInstanceId() 获取集群id
 * @method void setInstanceId(string $InstanceId) 设置集群id
 * @method string getDeviceCertificate() 获取设备证书
 * @method void setDeviceCertificate(string $DeviceCertificate) 设置设备证书
 * @method string getCaSn() 获取关联的CA证书SN
 * @method void setCaSn(string $CaSn) 设置关联的CA证书SN
 * @method string getClientId() 获取客户端ID
 * @method void setClientId(string $ClientId) 设置客户端ID
 * @method string getFormat() 获取证书格式
 * @method void setFormat(string $Format) 设置证书格式
 * @method string getStatus() 获取    ACTIVE,//激活     INACTIVE,//未激活     REVOKED,//吊销     PENDING_ACTIVATION,//注册待激活
 * @method void setStatus(string $Status) 设置    ACTIVE,//激活     INACTIVE,//未激活     REVOKED,//吊销     PENDING_ACTIVATION,//注册待激活
 */
class RegisterDeviceCertificateRequest extends AbstractModel
{
    /**
     * @var string 集群id
     */
    public $InstanceId;

    /**
     * @var string 设备证书
     */
    public $DeviceCertificate;

    /**
     * @var string 关联的CA证书SN
     */
    public $CaSn;

    /**
     * @var string 客户端ID
     */
    public $ClientId;

    /**
     * @var string 证书格式
     */
    public $Format;

    /**
     * @var string     ACTIVE,//激活     INACTIVE,//未激活     REVOKED,//吊销     PENDING_ACTIVATION,//注册待激活
     */
    public $Status;

    /**
     * @param string $InstanceId 集群id
     * @param string $DeviceCertificate 设备证书
     * @param string $CaSn 关联的CA证书SN
     * @param string $ClientId 客户端ID
     * @param string $Format 证书格式
     * @param string $Status     ACTIVE,//激活     INACTIVE,//未激活     REVOKED,//吊销     PENDING_ACTIVATION,//注册待激活
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
        if (array_key_exists("InstanceId",$param) and $param["InstanceId"] !== null) {
            $this->InstanceId = $param["InstanceId"];
        }

        if (array_key_exists("DeviceCertificate",$param) and $param["DeviceCertificate"] !== null) {
            $this->DeviceCertificate = $param["DeviceCertificate"];
        }

        if (array_key_exists("CaSn",$param) and $param["CaSn"] !== null) {
            $this->CaSn = $param["CaSn"];
        }

        if (array_key_exists("ClientId",$param) and $param["ClientId"] !== null) {
            $this->ClientId = $param["ClientId"];
        }

        if (array_key_exists("Format",$param) and $param["Format"] !== null) {
            $this->Format = $param["Format"];
        }

        if (array_key_exists("Status",$param) and $param["Status"] !== null) {
            $this->Status = $param["Status"];
        }
    }
}