<?php


namespace Xiaofei\MeishiSDK;

use Hanson\Foundation\AbstractAPI;

class Api extends AbstractAPI
{
    const DEV_URL = 'http://bac.a.51baibao.com:81/open/supplier/debug/sign';
    const PRODUCTION_URL = 'http://vip.nysochina.com';

    private $url;
    private $appKey;
    private $appSecret;
    private $authTime;
    private $sign;

    /**
     * Api constructor.
     * @param Weini $weini
     */
    public function __construct(Weini $weini)
    {
        $config = $weini->getConfig();
        $this->appKey = $config['appkey'] ?? '';
        $this->appSecret = $config['appsecret'] ?? '';
        $this->authTime = time();
        $this->url = $config['debug'] ? static::DEV_URL : static::PRODUCTION_URL;
    }

    /**
     * @param $params 请求的参数
     */
    private function makeSign($params){
        // foo=1, foobar=3, foobar=4
        $str1 = 'bar2foo1foobar3foobar4';

        // AppSecret 进⾏MD5加密，转成⼤写
        $str2 = strtoupper(md5($this->appSecret));

        // str3: 头信息内的 X-Auth-TimeStamp+X-Auth-Key+str1+str2
        $str3 = $this->authTime + $this->appKey + $str1 + $str2;

        return strtoupper(md5($str3));
    }

    /**
     * @param string $api
     * @param string $interfacename
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request(string $api, string $interfacename, array $params)
    {
        $this->sign = $this->makeSign();
        $response = $this->getHttp()->json($this->url . $api, $params);

        return json_decode(strval($response->getBody()));
    }

    // - X-Auth-Key: AppKey
    // - X-Auth-Sign: 签名
    // - X-Auth-TimeStamp: 秒值时间戳（⻓度为10）
    public function middlewares()
    {
        $this->http->addMiddleware($this->headerMiddleware([
            'X-Auth-Key' => $this->appKey,
            'X-Auth-Sign' => $this->sign,
            'X-Auth-TimeStamp' => $this->authTime
        ]));
    }
}
