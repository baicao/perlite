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
namespace TencentCloud\Teo\V20220901\Models;
use TencentCloud\Common\AbstractModel;

/**
 * DescribeTimingL7AnalysisData请求参数结构体
 *
 * @method string getStartTime() 获取开始时间。
 * @method void setStartTime(string $StartTime) 设置开始时间。
 * @method string getEndTime() 获取结束时间。
 * @method void setEndTime(string $EndTime) 设置结束时间。
 * @method array getMetricNames() 获取指标列表，取值有:
<li>l7Flow_outFlux: L7 EdgeOne 响应流量；</li>
<li>l7Flow_inFlux: L7 客户端请求流量；</li>
<li>l7Flow_flux: L7 访问总流量（EdgeOne 响应+客户端请求）；</li>
<li>l7Flow_outBandwidth: L7 EdgeOne 响应带宽；</li>
<li>l7Flow_inBandwidth：L7 客户端请求带宽；</li>
<li>l7Flow_bandwidth：L7 访问总带宽（EdgeOne 响应+客户端请求）；</li>
<li>l7Flow_request: L7 访问请求数。</li>
 * @method void setMetricNames(array $MetricNames) 设置指标列表，取值有:
<li>l7Flow_outFlux: L7 EdgeOne 响应流量；</li>
<li>l7Flow_inFlux: L7 客户端请求流量；</li>
<li>l7Flow_flux: L7 访问总流量（EdgeOne 响应+客户端请求）；</li>
<li>l7Flow_outBandwidth: L7 EdgeOne 响应带宽；</li>
<li>l7Flow_inBandwidth：L7 客户端请求带宽；</li>
<li>l7Flow_bandwidth：L7 访问总带宽（EdgeOne 响应+客户端请求）；</li>
<li>l7Flow_request: L7 访问请求数。</li>
 * @method array getZoneIds() 获取站点 ID 集合，此参数必填。
 * @method void setZoneIds(array $ZoneIds) 设置站点 ID 集合，此参数必填。
 * @method string getInterval() 获取查询时间粒度，取值有：
<li>min: 1分钟；</li>
<li>5min: 5分钟；</li>
<li>hour: 1小时；</li>
<li>day: 1天。</li>不填将根据开始时间跟结束时间的间距自动推算粒度，具体为：2 小时范围内以 min 粒度查询，2 天范围内以 5min 粒度查询，7 天范围内以 hour 粒度查询，超过 7 天以 day 粒度查询。
 * @method void setInterval(string $Interval) 设置查询时间粒度，取值有：
<li>min: 1分钟；</li>
<li>5min: 5分钟；</li>
<li>hour: 1小时；</li>
<li>day: 1天。</li>不填将根据开始时间跟结束时间的间距自动推算粒度，具体为：2 小时范围内以 min 粒度查询，2 天范围内以 5min 粒度查询，7 天范围内以 hour 粒度查询，超过 7 天以 day 粒度查询。
 * @method array getFilters() 获取过滤条件，详细的过滤条件 Key 值如下：
<li>country：按照国家/地区进行过滤，国家/地区遵循 <a href="https://baike.baidu.com/item/ISO%203166-1/5269555">ISO 3166-1 alpha-2</a> 规范。示例值：CN。</li>
<li>province：按照省份进行过滤，此参数只支持服务区域为中国大陆。省份代码参考<a href="https://cloud.tencent.com/document/product/228/6316#.E5.8C.BA.E5.9F.9F-.2F-.E8.BF.90.E8.90.A5.E5.95.86.E6.98.A0.E5.B0.84.E8.A1.A8">境内省份映射表</a>，示例值：22。</li>
<li>isp：按照运营商进行过滤，此参数只支持服务区域为中国大陆。对应的 Value 可选项如下：<br>   2：中国电信；<br>   26：中国联通；<br>   1046：中国移动；<br>   3947：中国铁通；<br>   38：教育网；<br>   43：长城宽带；<br>   0：其他运营商。</li>
<li>domain：按照子域名进行过滤，示例值： www.example.com。</li>
<li>url：按照 URL Path 进行过滤，示例值：/content 或 /content/test.jpg。若填写 url 参数，则最多可查询近 30 天的数据。</li>
<li>referer：按照 Referer 请求头部进行过滤，示例值：http://www.example.com/。若填写 referer 参数，则最多可查询近 30 天的数据；</li>
<li>resourceType：按照资源类型进行过滤，资源类型一般是文件后缀，示例值：.jpg。若填写 resourceType 参数，则最多可查询近 30 天的数据；</li>
<li>protocol：按照 HTTP 协议版本进行过滤。对应的 Value 可选项如下：<br>   HTTP/1.0；<br>   HTTP/1.1；<br>   HTTP/2.0；<br>   HTTP/3；<br>   WebSocket。</li>
<li>socket：按照 HTTP协议类型进行过滤。对应的 Value 可选项如下：<br>   HTTP：HTTP 协议；<br>   HTTPS：HTTPS 协议；<br>   QUIC：QUIC 协议。</li>
<li>statusCode：按照边缘状态码进行过滤。若填写 statusCode 参数，则最多可查询近 30 天的数据。对应的 Value 可选项如下：<br>   1XX：1xx类型的状态码；<br>   2XX：2xx类型的状态码；<br>   3XX：3xx类型的状态码；<br>   4XX：4xx类型的状态码；<br>   5XX：5xx类型的状态码；<br>   在 [0,600) 范围内的整数。</li>
<li>browserType：按照浏览器类型进行过滤。若填写 browserType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   Firefox：Firefox浏览器；<br>   Chrome：Chrome浏览器；<br>   Safari：Safari浏览器；<br>   Other：其他浏览器类型；<br>   Empty：浏览器类型为空；<br>   Bot：搜索引擎爬虫；<br>   MicrosoftEdge：MicrosoftEdge浏览器；<br>   IE：IE浏览器；<br>   Opera：Opera浏览器；<br>   QQBrowser：QQ浏览器；<br>   LBBrowser：LB浏览器；<br>   MaxthonBrowser：Maxthon浏览器；<br>   SouGouBrowser：搜狗浏览器；<br>   BIDUBrowser：百度浏览器；<br>   TaoBrowser：淘浏览器；<br>   UBrowser：UC浏览器。</li>
<li>deviceType：按照设备类型进行过滤。若填写 deviceType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   TV：TV设备；<br>   Tablet：Tablet设备；<br>   Mobile：Mobile设备；<br>   Desktop：Desktop设备；<br>   Other：其他设备类型；<br>   Empty：设备类型为空。</li>
<li>operatingSystemType：按照操作系统类型进行过滤。若填写 operatingSystemType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   Linux：Linux操作系统；<br>   MacOS：MacOs操作系统；<br>   Android：Android操作系统；<br>   IOS：IOS操作系统；<br>   Windows：Windows操作系统；<br>   NetBSD：NetBSD；<br>   ChromiumOS：ChromiumOS；<br>   Bot：搜索引擎爬虫；<br>   Other：其他类型的操作系统；<br>   Empty：操作系统为空。</li>
<li>tlsVersion：按照 TLS 版本进行过滤。若填写 tlsVersion 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   TLS1.0；<br>   TLS1.1；<br>   TLS1.2；<br>   TLS1.3。</li>
<li>ipVersion：按照 IP 版本进行过滤。对应 Value 的可选项如下：<br>   4：IPv4；<br>   6：IPv6。</li>
<li>cacheType：按照缓存状态进行过滤。对应 Value 的可选项如下：<br>   hit：请求命中 EdgeOne 节点缓存，资源由节点缓存提供。资源部分命中缓存也会记录为 hit。<br>   miss：请求未命中 EdgeOne 节点缓存，资源由源站提供。<br>   dynamic：请求的资源无法缓存/未配置被节点缓存，资源由源站提供。<br>   other：无法被识别的缓存状态。边缘函数响应的请求会记录为 other。</li>
<li>clientIp：按照客户端 IP 进行过滤。</li>
 * @method void setFilters(array $Filters) 设置过滤条件，详细的过滤条件 Key 值如下：
<li>country：按照国家/地区进行过滤，国家/地区遵循 <a href="https://baike.baidu.com/item/ISO%203166-1/5269555">ISO 3166-1 alpha-2</a> 规范。示例值：CN。</li>
<li>province：按照省份进行过滤，此参数只支持服务区域为中国大陆。省份代码参考<a href="https://cloud.tencent.com/document/product/228/6316#.E5.8C.BA.E5.9F.9F-.2F-.E8.BF.90.E8.90.A5.E5.95.86.E6.98.A0.E5.B0.84.E8.A1.A8">境内省份映射表</a>，示例值：22。</li>
<li>isp：按照运营商进行过滤，此参数只支持服务区域为中国大陆。对应的 Value 可选项如下：<br>   2：中国电信；<br>   26：中国联通；<br>   1046：中国移动；<br>   3947：中国铁通；<br>   38：教育网；<br>   43：长城宽带；<br>   0：其他运营商。</li>
<li>domain：按照子域名进行过滤，示例值： www.example.com。</li>
<li>url：按照 URL Path 进行过滤，示例值：/content 或 /content/test.jpg。若填写 url 参数，则最多可查询近 30 天的数据。</li>
<li>referer：按照 Referer 请求头部进行过滤，示例值：http://www.example.com/。若填写 referer 参数，则最多可查询近 30 天的数据；</li>
<li>resourceType：按照资源类型进行过滤，资源类型一般是文件后缀，示例值：.jpg。若填写 resourceType 参数，则最多可查询近 30 天的数据；</li>
<li>protocol：按照 HTTP 协议版本进行过滤。对应的 Value 可选项如下：<br>   HTTP/1.0；<br>   HTTP/1.1；<br>   HTTP/2.0；<br>   HTTP/3；<br>   WebSocket。</li>
<li>socket：按照 HTTP协议类型进行过滤。对应的 Value 可选项如下：<br>   HTTP：HTTP 协议；<br>   HTTPS：HTTPS 协议；<br>   QUIC：QUIC 协议。</li>
<li>statusCode：按照边缘状态码进行过滤。若填写 statusCode 参数，则最多可查询近 30 天的数据。对应的 Value 可选项如下：<br>   1XX：1xx类型的状态码；<br>   2XX：2xx类型的状态码；<br>   3XX：3xx类型的状态码；<br>   4XX：4xx类型的状态码；<br>   5XX：5xx类型的状态码；<br>   在 [0,600) 范围内的整数。</li>
<li>browserType：按照浏览器类型进行过滤。若填写 browserType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   Firefox：Firefox浏览器；<br>   Chrome：Chrome浏览器；<br>   Safari：Safari浏览器；<br>   Other：其他浏览器类型；<br>   Empty：浏览器类型为空；<br>   Bot：搜索引擎爬虫；<br>   MicrosoftEdge：MicrosoftEdge浏览器；<br>   IE：IE浏览器；<br>   Opera：Opera浏览器；<br>   QQBrowser：QQ浏览器；<br>   LBBrowser：LB浏览器；<br>   MaxthonBrowser：Maxthon浏览器；<br>   SouGouBrowser：搜狗浏览器；<br>   BIDUBrowser：百度浏览器；<br>   TaoBrowser：淘浏览器；<br>   UBrowser：UC浏览器。</li>
<li>deviceType：按照设备类型进行过滤。若填写 deviceType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   TV：TV设备；<br>   Tablet：Tablet设备；<br>   Mobile：Mobile设备；<br>   Desktop：Desktop设备；<br>   Other：其他设备类型；<br>   Empty：设备类型为空。</li>
<li>operatingSystemType：按照操作系统类型进行过滤。若填写 operatingSystemType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   Linux：Linux操作系统；<br>   MacOS：MacOs操作系统；<br>   Android：Android操作系统；<br>   IOS：IOS操作系统；<br>   Windows：Windows操作系统；<br>   NetBSD：NetBSD；<br>   ChromiumOS：ChromiumOS；<br>   Bot：搜索引擎爬虫；<br>   Other：其他类型的操作系统；<br>   Empty：操作系统为空。</li>
<li>tlsVersion：按照 TLS 版本进行过滤。若填写 tlsVersion 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   TLS1.0；<br>   TLS1.1；<br>   TLS1.2；<br>   TLS1.3。</li>
<li>ipVersion：按照 IP 版本进行过滤。对应 Value 的可选项如下：<br>   4：IPv4；<br>   6：IPv6。</li>
<li>cacheType：按照缓存状态进行过滤。对应 Value 的可选项如下：<br>   hit：请求命中 EdgeOne 节点缓存，资源由节点缓存提供。资源部分命中缓存也会记录为 hit。<br>   miss：请求未命中 EdgeOne 节点缓存，资源由源站提供。<br>   dynamic：请求的资源无法缓存/未配置被节点缓存，资源由源站提供。<br>   other：无法被识别的缓存状态。边缘函数响应的请求会记录为 other。</li>
<li>clientIp：按照客户端 IP 进行过滤。</li>
 * @method string getArea() 获取数据归属地区，取值有：
<li>overseas：全球（除中国大陆地区）数据；</li>
<li>mainland：中国大陆地区数据；</li>
<li>global：全球数据。</li>
不填默认取值为 global。
 * @method void setArea(string $Area) 设置数据归属地区，取值有：
<li>overseas：全球（除中国大陆地区）数据；</li>
<li>mainland：中国大陆地区数据；</li>
<li>global：全球数据。</li>
不填默认取值为 global。
 */
class DescribeTimingL7AnalysisDataRequest extends AbstractModel
{
    /**
     * @var string 开始时间。
     */
    public $StartTime;

    /**
     * @var string 结束时间。
     */
    public $EndTime;

    /**
     * @var array 指标列表，取值有:
<li>l7Flow_outFlux: L7 EdgeOne 响应流量；</li>
<li>l7Flow_inFlux: L7 客户端请求流量；</li>
<li>l7Flow_flux: L7 访问总流量（EdgeOne 响应+客户端请求）；</li>
<li>l7Flow_outBandwidth: L7 EdgeOne 响应带宽；</li>
<li>l7Flow_inBandwidth：L7 客户端请求带宽；</li>
<li>l7Flow_bandwidth：L7 访问总带宽（EdgeOne 响应+客户端请求）；</li>
<li>l7Flow_request: L7 访问请求数。</li>
     */
    public $MetricNames;

    /**
     * @var array 站点 ID 集合，此参数必填。
     */
    public $ZoneIds;

    /**
     * @var string 查询时间粒度，取值有：
<li>min: 1分钟；</li>
<li>5min: 5分钟；</li>
<li>hour: 1小时；</li>
<li>day: 1天。</li>不填将根据开始时间跟结束时间的间距自动推算粒度，具体为：2 小时范围内以 min 粒度查询，2 天范围内以 5min 粒度查询，7 天范围内以 hour 粒度查询，超过 7 天以 day 粒度查询。
     */
    public $Interval;

    /**
     * @var array 过滤条件，详细的过滤条件 Key 值如下：
<li>country：按照国家/地区进行过滤，国家/地区遵循 <a href="https://baike.baidu.com/item/ISO%203166-1/5269555">ISO 3166-1 alpha-2</a> 规范。示例值：CN。</li>
<li>province：按照省份进行过滤，此参数只支持服务区域为中国大陆。省份代码参考<a href="https://cloud.tencent.com/document/product/228/6316#.E5.8C.BA.E5.9F.9F-.2F-.E8.BF.90.E8.90.A5.E5.95.86.E6.98.A0.E5.B0.84.E8.A1.A8">境内省份映射表</a>，示例值：22。</li>
<li>isp：按照运营商进行过滤，此参数只支持服务区域为中国大陆。对应的 Value 可选项如下：<br>   2：中国电信；<br>   26：中国联通；<br>   1046：中国移动；<br>   3947：中国铁通；<br>   38：教育网；<br>   43：长城宽带；<br>   0：其他运营商。</li>
<li>domain：按照子域名进行过滤，示例值： www.example.com。</li>
<li>url：按照 URL Path 进行过滤，示例值：/content 或 /content/test.jpg。若填写 url 参数，则最多可查询近 30 天的数据。</li>
<li>referer：按照 Referer 请求头部进行过滤，示例值：http://www.example.com/。若填写 referer 参数，则最多可查询近 30 天的数据；</li>
<li>resourceType：按照资源类型进行过滤，资源类型一般是文件后缀，示例值：.jpg。若填写 resourceType 参数，则最多可查询近 30 天的数据；</li>
<li>protocol：按照 HTTP 协议版本进行过滤。对应的 Value 可选项如下：<br>   HTTP/1.0；<br>   HTTP/1.1；<br>   HTTP/2.0；<br>   HTTP/3；<br>   WebSocket。</li>
<li>socket：按照 HTTP协议类型进行过滤。对应的 Value 可选项如下：<br>   HTTP：HTTP 协议；<br>   HTTPS：HTTPS 协议；<br>   QUIC：QUIC 协议。</li>
<li>statusCode：按照边缘状态码进行过滤。若填写 statusCode 参数，则最多可查询近 30 天的数据。对应的 Value 可选项如下：<br>   1XX：1xx类型的状态码；<br>   2XX：2xx类型的状态码；<br>   3XX：3xx类型的状态码；<br>   4XX：4xx类型的状态码；<br>   5XX：5xx类型的状态码；<br>   在 [0,600) 范围内的整数。</li>
<li>browserType：按照浏览器类型进行过滤。若填写 browserType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   Firefox：Firefox浏览器；<br>   Chrome：Chrome浏览器；<br>   Safari：Safari浏览器；<br>   Other：其他浏览器类型；<br>   Empty：浏览器类型为空；<br>   Bot：搜索引擎爬虫；<br>   MicrosoftEdge：MicrosoftEdge浏览器；<br>   IE：IE浏览器；<br>   Opera：Opera浏览器；<br>   QQBrowser：QQ浏览器；<br>   LBBrowser：LB浏览器；<br>   MaxthonBrowser：Maxthon浏览器；<br>   SouGouBrowser：搜狗浏览器；<br>   BIDUBrowser：百度浏览器；<br>   TaoBrowser：淘浏览器；<br>   UBrowser：UC浏览器。</li>
<li>deviceType：按照设备类型进行过滤。若填写 deviceType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   TV：TV设备；<br>   Tablet：Tablet设备；<br>   Mobile：Mobile设备；<br>   Desktop：Desktop设备；<br>   Other：其他设备类型；<br>   Empty：设备类型为空。</li>
<li>operatingSystemType：按照操作系统类型进行过滤。若填写 operatingSystemType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   Linux：Linux操作系统；<br>   MacOS：MacOs操作系统；<br>   Android：Android操作系统；<br>   IOS：IOS操作系统；<br>   Windows：Windows操作系统；<br>   NetBSD：NetBSD；<br>   ChromiumOS：ChromiumOS；<br>   Bot：搜索引擎爬虫；<br>   Other：其他类型的操作系统；<br>   Empty：操作系统为空。</li>
<li>tlsVersion：按照 TLS 版本进行过滤。若填写 tlsVersion 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   TLS1.0；<br>   TLS1.1；<br>   TLS1.2；<br>   TLS1.3。</li>
<li>ipVersion：按照 IP 版本进行过滤。对应 Value 的可选项如下：<br>   4：IPv4；<br>   6：IPv6。</li>
<li>cacheType：按照缓存状态进行过滤。对应 Value 的可选项如下：<br>   hit：请求命中 EdgeOne 节点缓存，资源由节点缓存提供。资源部分命中缓存也会记录为 hit。<br>   miss：请求未命中 EdgeOne 节点缓存，资源由源站提供。<br>   dynamic：请求的资源无法缓存/未配置被节点缓存，资源由源站提供。<br>   other：无法被识别的缓存状态。边缘函数响应的请求会记录为 other。</li>
<li>clientIp：按照客户端 IP 进行过滤。</li>
     */
    public $Filters;

    /**
     * @var string 数据归属地区，取值有：
<li>overseas：全球（除中国大陆地区）数据；</li>
<li>mainland：中国大陆地区数据；</li>
<li>global：全球数据。</li>
不填默认取值为 global。
     */
    public $Area;

    /**
     * @param string $StartTime 开始时间。
     * @param string $EndTime 结束时间。
     * @param array $MetricNames 指标列表，取值有:
<li>l7Flow_outFlux: L7 EdgeOne 响应流量；</li>
<li>l7Flow_inFlux: L7 客户端请求流量；</li>
<li>l7Flow_flux: L7 访问总流量（EdgeOne 响应+客户端请求）；</li>
<li>l7Flow_outBandwidth: L7 EdgeOne 响应带宽；</li>
<li>l7Flow_inBandwidth：L7 客户端请求带宽；</li>
<li>l7Flow_bandwidth：L7 访问总带宽（EdgeOne 响应+客户端请求）；</li>
<li>l7Flow_request: L7 访问请求数。</li>
     * @param array $ZoneIds 站点 ID 集合，此参数必填。
     * @param string $Interval 查询时间粒度，取值有：
<li>min: 1分钟；</li>
<li>5min: 5分钟；</li>
<li>hour: 1小时；</li>
<li>day: 1天。</li>不填将根据开始时间跟结束时间的间距自动推算粒度，具体为：2 小时范围内以 min 粒度查询，2 天范围内以 5min 粒度查询，7 天范围内以 hour 粒度查询，超过 7 天以 day 粒度查询。
     * @param array $Filters 过滤条件，详细的过滤条件 Key 值如下：
<li>country：按照国家/地区进行过滤，国家/地区遵循 <a href="https://baike.baidu.com/item/ISO%203166-1/5269555">ISO 3166-1 alpha-2</a> 规范。示例值：CN。</li>
<li>province：按照省份进行过滤，此参数只支持服务区域为中国大陆。省份代码参考<a href="https://cloud.tencent.com/document/product/228/6316#.E5.8C.BA.E5.9F.9F-.2F-.E8.BF.90.E8.90.A5.E5.95.86.E6.98.A0.E5.B0.84.E8.A1.A8">境内省份映射表</a>，示例值：22。</li>
<li>isp：按照运营商进行过滤，此参数只支持服务区域为中国大陆。对应的 Value 可选项如下：<br>   2：中国电信；<br>   26：中国联通；<br>   1046：中国移动；<br>   3947：中国铁通；<br>   38：教育网；<br>   43：长城宽带；<br>   0：其他运营商。</li>
<li>domain：按照子域名进行过滤，示例值： www.example.com。</li>
<li>url：按照 URL Path 进行过滤，示例值：/content 或 /content/test.jpg。若填写 url 参数，则最多可查询近 30 天的数据。</li>
<li>referer：按照 Referer 请求头部进行过滤，示例值：http://www.example.com/。若填写 referer 参数，则最多可查询近 30 天的数据；</li>
<li>resourceType：按照资源类型进行过滤，资源类型一般是文件后缀，示例值：.jpg。若填写 resourceType 参数，则最多可查询近 30 天的数据；</li>
<li>protocol：按照 HTTP 协议版本进行过滤。对应的 Value 可选项如下：<br>   HTTP/1.0；<br>   HTTP/1.1；<br>   HTTP/2.0；<br>   HTTP/3；<br>   WebSocket。</li>
<li>socket：按照 HTTP协议类型进行过滤。对应的 Value 可选项如下：<br>   HTTP：HTTP 协议；<br>   HTTPS：HTTPS 协议；<br>   QUIC：QUIC 协议。</li>
<li>statusCode：按照边缘状态码进行过滤。若填写 statusCode 参数，则最多可查询近 30 天的数据。对应的 Value 可选项如下：<br>   1XX：1xx类型的状态码；<br>   2XX：2xx类型的状态码；<br>   3XX：3xx类型的状态码；<br>   4XX：4xx类型的状态码；<br>   5XX：5xx类型的状态码；<br>   在 [0,600) 范围内的整数。</li>
<li>browserType：按照浏览器类型进行过滤。若填写 browserType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   Firefox：Firefox浏览器；<br>   Chrome：Chrome浏览器；<br>   Safari：Safari浏览器；<br>   Other：其他浏览器类型；<br>   Empty：浏览器类型为空；<br>   Bot：搜索引擎爬虫；<br>   MicrosoftEdge：MicrosoftEdge浏览器；<br>   IE：IE浏览器；<br>   Opera：Opera浏览器；<br>   QQBrowser：QQ浏览器；<br>   LBBrowser：LB浏览器；<br>   MaxthonBrowser：Maxthon浏览器；<br>   SouGouBrowser：搜狗浏览器；<br>   BIDUBrowser：百度浏览器；<br>   TaoBrowser：淘浏览器；<br>   UBrowser：UC浏览器。</li>
<li>deviceType：按照设备类型进行过滤。若填写 deviceType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   TV：TV设备；<br>   Tablet：Tablet设备；<br>   Mobile：Mobile设备；<br>   Desktop：Desktop设备；<br>   Other：其他设备类型；<br>   Empty：设备类型为空。</li>
<li>operatingSystemType：按照操作系统类型进行过滤。若填写 operatingSystemType 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   Linux：Linux操作系统；<br>   MacOS：MacOs操作系统；<br>   Android：Android操作系统；<br>   IOS：IOS操作系统；<br>   Windows：Windows操作系统；<br>   NetBSD：NetBSD；<br>   ChromiumOS：ChromiumOS；<br>   Bot：搜索引擎爬虫；<br>   Other：其他类型的操作系统；<br>   Empty：操作系统为空。</li>
<li>tlsVersion：按照 TLS 版本进行过滤。若填写 tlsVersion 参数，则最多可查询近 30 天的数据。对应 Value 的可选项如下：<br>   TLS1.0；<br>   TLS1.1；<br>   TLS1.2；<br>   TLS1.3。</li>
<li>ipVersion：按照 IP 版本进行过滤。对应 Value 的可选项如下：<br>   4：IPv4；<br>   6：IPv6。</li>
<li>cacheType：按照缓存状态进行过滤。对应 Value 的可选项如下：<br>   hit：请求命中 EdgeOne 节点缓存，资源由节点缓存提供。资源部分命中缓存也会记录为 hit。<br>   miss：请求未命中 EdgeOne 节点缓存，资源由源站提供。<br>   dynamic：请求的资源无法缓存/未配置被节点缓存，资源由源站提供。<br>   other：无法被识别的缓存状态。边缘函数响应的请求会记录为 other。</li>
<li>clientIp：按照客户端 IP 进行过滤。</li>
     * @param string $Area 数据归属地区，取值有：
<li>overseas：全球（除中国大陆地区）数据；</li>
<li>mainland：中国大陆地区数据；</li>
<li>global：全球数据。</li>
不填默认取值为 global。
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
        if (array_key_exists("StartTime",$param) and $param["StartTime"] !== null) {
            $this->StartTime = $param["StartTime"];
        }

        if (array_key_exists("EndTime",$param) and $param["EndTime"] !== null) {
            $this->EndTime = $param["EndTime"];
        }

        if (array_key_exists("MetricNames",$param) and $param["MetricNames"] !== null) {
            $this->MetricNames = $param["MetricNames"];
        }

        if (array_key_exists("ZoneIds",$param) and $param["ZoneIds"] !== null) {
            $this->ZoneIds = $param["ZoneIds"];
        }

        if (array_key_exists("Interval",$param) and $param["Interval"] !== null) {
            $this->Interval = $param["Interval"];
        }

        if (array_key_exists("Filters",$param) and $param["Filters"] !== null) {
            $this->Filters = [];
            foreach ($param["Filters"] as $key => $value){
                $obj = new QueryCondition();
                $obj->deserialize($value);
                array_push($this->Filters, $obj);
            }
        }

        if (array_key_exists("Area",$param) and $param["Area"] !== null) {
            $this->Area = $param["Area"];
        }
    }
}