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
namespace TencentCloud\Vpc\V20170312\Models;
use TencentCloud\Common\AbstractModel;

/**
 * CreateHaVip请求参数结构体
 *
 * @method string getVpcId() 获取`HAVIP`所在私有网络`ID`。
 * @method void setVpcId(string $VpcId) 设置`HAVIP`所在私有网络`ID`。
 * @method string getHaVipName() 获取`HAVIP`名称。
 * @method void setHaVipName(string $HaVipName) 设置`HAVIP`名称。
 * @method string getSubnetId() 获取`HAVIP`所在子网`ID`。
 * @method void setSubnetId(string $SubnetId) 设置`HAVIP`所在子网`ID`。
 * @method string getVip() 获取指定虚拟IP地址，必须在`VPC`网段内且未被占用。不指定则自动分配。
 * @method void setVip(string $Vip) 设置指定虚拟IP地址，必须在`VPC`网段内且未被占用。不指定则自动分配。
 * @method string getNetworkInterfaceId() 获取`HAVIP`所在弹性网卡`ID`。
 * @method void setNetworkInterfaceId(string $NetworkInterfaceId) 设置`HAVIP`所在弹性网卡`ID`。
 * @method boolean getCheckAssociate() 获取是否开启`HAVIP`漂移时子机或网卡范围的校验。默认不开启。
 * @method void setCheckAssociate(boolean $CheckAssociate) 设置是否开启`HAVIP`漂移时子机或网卡范围的校验。默认不开启。
 * @method array getTags() 获取指定绑定的标签列表，例如：[{"Key": "city", "Value": "shanghai"}]。
 * @method void setTags(array $Tags) 设置指定绑定的标签列表，例如：[{"Key": "city", "Value": "shanghai"}]。
 */
class CreateHaVipRequest extends AbstractModel
{
    /**
     * @var string `HAVIP`所在私有网络`ID`。
     */
    public $VpcId;

    /**
     * @var string `HAVIP`名称。
     */
    public $HaVipName;

    /**
     * @var string `HAVIP`所在子网`ID`。
     */
    public $SubnetId;

    /**
     * @var string 指定虚拟IP地址，必须在`VPC`网段内且未被占用。不指定则自动分配。
     */
    public $Vip;

    /**
     * @var string `HAVIP`所在弹性网卡`ID`。
     */
    public $NetworkInterfaceId;

    /**
     * @var boolean 是否开启`HAVIP`漂移时子机或网卡范围的校验。默认不开启。
     */
    public $CheckAssociate;

    /**
     * @var array 指定绑定的标签列表，例如：[{"Key": "city", "Value": "shanghai"}]。
     */
    public $Tags;

    /**
     * @param string $VpcId `HAVIP`所在私有网络`ID`。
     * @param string $HaVipName `HAVIP`名称。
     * @param string $SubnetId `HAVIP`所在子网`ID`。
     * @param string $Vip 指定虚拟IP地址，必须在`VPC`网段内且未被占用。不指定则自动分配。
     * @param string $NetworkInterfaceId `HAVIP`所在弹性网卡`ID`。
     * @param boolean $CheckAssociate 是否开启`HAVIP`漂移时子机或网卡范围的校验。默认不开启。
     * @param array $Tags 指定绑定的标签列表，例如：[{"Key": "city", "Value": "shanghai"}]。
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
        if (array_key_exists("VpcId",$param) and $param["VpcId"] !== null) {
            $this->VpcId = $param["VpcId"];
        }

        if (array_key_exists("HaVipName",$param) and $param["HaVipName"] !== null) {
            $this->HaVipName = $param["HaVipName"];
        }

        if (array_key_exists("SubnetId",$param) and $param["SubnetId"] !== null) {
            $this->SubnetId = $param["SubnetId"];
        }

        if (array_key_exists("Vip",$param) and $param["Vip"] !== null) {
            $this->Vip = $param["Vip"];
        }

        if (array_key_exists("NetworkInterfaceId",$param) and $param["NetworkInterfaceId"] !== null) {
            $this->NetworkInterfaceId = $param["NetworkInterfaceId"];
        }

        if (array_key_exists("CheckAssociate",$param) and $param["CheckAssociate"] !== null) {
            $this->CheckAssociate = $param["CheckAssociate"];
        }

        if (array_key_exists("Tags",$param) and $param["Tags"] !== null) {
            $this->Tags = [];
            foreach ($param["Tags"] as $key => $value){
                $obj = new Tag();
                $obj->deserialize($value);
                array_push($this->Tags, $obj);
            }
        }
    }
}
