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
namespace TencentCloud\Cynosdb\V20190107\Models;
use TencentCloud\Common\AbstractModel;

/**
 * TDSQL-C MySQL支持的proxy版本信息
 *
 * @method string getProxyVersion() 获取proxy版本号
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setProxyVersion(string $ProxyVersion) 设置proxy版本号
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getProxyVersionType() 获取版本描述：GA:稳定版  BETA:尝鲜版，DEPRECATED:过旧，
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setProxyVersionType(string $ProxyVersionType) 设置版本描述：GA:稳定版  BETA:尝鲜版，DEPRECATED:过旧，
注意：此字段可能返回 null，表示取不到有效值。
 */
class ProxyVersionInfo extends AbstractModel
{
    /**
     * @var string proxy版本号
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $ProxyVersion;

    /**
     * @var string 版本描述：GA:稳定版  BETA:尝鲜版，DEPRECATED:过旧，
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $ProxyVersionType;

    /**
     * @param string $ProxyVersion proxy版本号
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $ProxyVersionType 版本描述：GA:稳定版  BETA:尝鲜版，DEPRECATED:过旧，
注意：此字段可能返回 null，表示取不到有效值。
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
        if (array_key_exists("ProxyVersion",$param) and $param["ProxyVersion"] !== null) {
            $this->ProxyVersion = $param["ProxyVersion"];
        }

        if (array_key_exists("ProxyVersionType",$param) and $param["ProxyVersionType"] !== null) {
            $this->ProxyVersionType = $param["ProxyVersionType"];
        }
    }
}