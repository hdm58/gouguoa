<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.gougucms.com
 */

namespace app\api\service;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

/**
 * 单例 一次请求中所有出现jwt的地方都是一个用户
 * Class JwtAuth
 * @package app\api\service
 */
class JwtAuth
{
    // jwt token
    private $token;

    // jwt 过期时间
    private $expTime = 3600;

    // claim iss 签发组织
    private $iss = 'wwww.gougucms.com';

    // claim aud签发作者
    private $aud = 'gougucms';

    // secrect
    private $secrect = 'GOUGUCMS';

    // claim uid
    private $uid;

    // decode token
    private $decodeToken;

    // 单例模式JwtAuth句柄
    private static $instance;

    // 获取JwtAuth的句柄
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 私有化构造函数
    public function __construct()
    {
        // jwt 过期时间
        $this->expTime = get_system_config('token','exptime');
        // claim iss 签发组织
        $this->iss = get_system_config('token','iss');
        // claim aud签发作者
        $this->aud = get_system_config('token','aud');
        // secrect
        $this->secrect = get_system_config('token','secrect');
    }

    // 私有化clone函数
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    // 获取token
    public function getToken()
    {
        return (string) $this->token;
    }

    // 设置token
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    // 设置uid
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    // 获取uid
    public function getUid()
    {
        return $this->uid;
    }

    // 编码jwt token
    public function encode()
    {
        $time = time(); //签发时间
        $this->token = (new Builder())->setHeader('alg', 'HS256')
            ->setIssuer($this->iss)
            ->setAudience($this->aud)
            ->setIssuedAt($time)
            ->setExpiration($time + $this->expTime)
            ->set('uid', $this->uid)
            ->sign(new Sha256(), $this->secrect)
            ->getToken();

        return $this;
    }

    public function decode()
    {
        if (!$this->decodeToken) {
            $this->decodeToken = (new Parser())->parse((string) $this->token); // Parses from a string
            $this->uid = $this->decodeToken->getClaim('uid');
        }
        return $this->decodeToken;
    }

    // validate
    public function validate()
    {
        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer($this->iss);
        $data->setAudience($this->aud);
        $data->setId($this->uid);

        return $this->decode()->validate($data);
    }

    // verify token
    public function verify()
    {
        $signer = new Sha256();
        return $this->decode()->verify($signer, $this->secrect);
    }

}
