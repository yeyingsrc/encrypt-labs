# encrypt-labs
> 说明：之前自己在学习前端加解密经常遇到加密解不了的情况；之后慢慢看师傅们的文章，也学到了很多绕过技术，于是写了个简单的靶场，为之后的师傅们铺路学习
> 加密方式列出了我经常见的7种方式

js代码做了混淆，感觉较难分析的可以使用demo(明文版).php进行练习

- 在线体验地址：http://82.156.57.228:43899/
- 配合其他的github项目和文章进行练习<br>

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

<img width="1262" alt="image" src="https://github.com/user-attachments/assets/5fb8a258-8500-42fa-9410-9311f59b2583">

## 列举几个数据包中的图
### AES
<img width="1156" alt="image" src="https://github.com/user-attachments/assets/7b97cb86-163c-48ac-b777-bdb7bb7c8ce5">

### 非对称加密
<img width="1158" alt="image" src="https://github.com/user-attachments/assets/6e812dc0-9f5a-4cd9-b201-c57ff5240a7c">

### AES与RSA
<img width="1156" alt="image" src="https://github.com/user-attachments/assets/95396225-11d6-4971-8641-70a65275127a">

### 签名的方式
<img width="1155" alt="image" src="https://github.com/user-attachments/assets/c88b41f4-075b-4eae-8a1c-4b57a27c6370">




