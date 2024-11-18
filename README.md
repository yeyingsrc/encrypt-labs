# encrypt-labs
> 说明：之前自己在学习前端加解密经常遇到加密解不了的情况；之后慢慢看师傅们的文章，也学到了很多绕过技术，于是写了个简单的靶场，为之后的师傅们铺路学习,加密方式列出了我经常见的8种方式包含非对称加密、对称加密、加签以及禁止重放的测试场景，比如AES、DES、RSA，用于渗透测试加解密练习。希望可以帮助到最近在学习这块知识的师傅，靶场函数.很简单，和实战相比还是差的有点多，不过应该够入门了

js代码做了混淆，感觉较难分析的可以使用easy.php进行练习

- 在线体验地址：

默认密码：admin/123456

http://82.156.57.228:43899  (混淆)

http://82.156.57.228:43899/easy.php （无混淆）

## 食用方式:

Nginx+mysql+php8放在网站路径即可食用

Ta0ing师傅提供了docker版本
```
git clone https://github.com/Ta0ing/encrypt-labs-docker.git
docker-compose up -d --build
```
<img width="1326" alt="image" src="https://github.com/user-attachments/assets/c19d3edb-ad2c-4a51-9f52-c0bd384ffb67">
<img width="1837" alt="image" src="https://github.com/user-attachments/assets/b73967bd-9106-486c-b2df-4614147806dc">

如果对你有帮助的话，来点🌟🌟🌟，食用起来会更香！


配合其他的github项目和文章进行练习<br>


**工具脚本**
  
[burp自动加解密插件autoDeceder](https://github.com/f0ng/autoDecoder)

[前端JS加密绕过脚本JS-Forward](https://github.com/G-Security-Team/JS-Forward)

[JsRpc脚本](https://github.com/jxhczhl/JsRpc)

**文章**

[渗透测试高级技巧：分析验签与前端加密（一）](https://mp.weixin.qq.com/s?__biz=Mzk0MTM4NzIxMQ==&mid=2247511526&idx=1&sn=c3a661b71ad9e9108b822cec461d34e6&chksm=c2d1d142f5a65854cd0ac07ec38d46c514f472734923fad4ce8bb85579b5a056447abcb545da&scene=21#wechat_redirect)

[渗透测试高级技巧（二）：对抗前端动态密钥与非对称加密防护](https://mp.weixin.qq.com/s/gMbbEV62XR5_QCACQwZnOw)

[保姆级教程--前端加密的对抗（附带靶场）](https://mp.weixin.qq.com/s/_WdQlH6AKma8zYq73Ub7ag)

<img width="1508" alt="image" src="https://github.com/user-attachments/assets/c7c20268-9eb3-4431-b7c1-712710b4ac60">

## 主要分为对称加密、非对称加密和加签的方式

<img width="1400" alt="image" src="https://github.com/user-attachments/assets/cab7b27b-793c-47cc-b7ae-9f318a40492f">

## 列举几个数据包中的图
### AES
<img width="1156" alt="image" src="https://github.com/user-attachments/assets/7b97cb86-163c-48ac-b777-bdb7bb7c8ce5">

### 非对称加密
<img width="1158" alt="image" src="https://github.com/user-attachments/assets/6e812dc0-9f5a-4cd9-b201-c57ff5240a7c">

### AES与RSA
<img width="1156" alt="image" src="https://github.com/user-attachments/assets/95396225-11d6-4971-8641-70a65275127a">

### 签名的方式
<img width="1155" alt="image" src="https://github.com/user-attachments/assets/c88b41f4-075b-4eae-8a1c-4b57a27c6370">

### 禁止重放
<img width="1198" alt="image" src="https://github.com/user-attachments/assets/0a75135e-a197-47f3-bd34-858d27a5c19d">


## 🔯 Stars
[![Stargazers over time](https://starchart.cc/SwagXz/encrypt-labs.svg?variant=adaptive)](https://starchart.cc/SwagXz/encrypt-labs)



