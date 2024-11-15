// 监听表单提交事件，仅在点击登录时显示弹窗
document.getElementById("loginForm")
	.addEventListener("submit", function(event) {
		event.preventDefault(); // 阻止表单默认提交

		// 显示弹窗
		document.getElementById("modal")
			.style.display = "flex";
	});

// 发送数据到指定接口
function sendDataAes(url) {
	// 获取表单数据并将其转换为 JSON 字符串
	const formData = {
		username: document.getElementById("username")
			.value,
		password: document.getElementById("password")
			.value
	};
	const jsonData = JSON.stringify(formData);

	// 设置密钥和 IV，需与后端一致
	const key = CryptoJS.enc.Utf8.parse("1234567890123456");
	const iv = CryptoJS.enc.Utf8.parse("1234567890123456");

	// 使用 AES 加密 JSON 数据
	const encrypted = CryptoJS.AES.encrypt(jsonData, key, {
			iv: iv,
			mode: CryptoJS.mode.CBC,
			padding: CryptoJS.pad.Pkcs7
		})
		.toString();
    // 将加密后的数据格式化为查询字符串
    const params = `encryptedData=${encodeURIComponent(encrypted)}`;

	// 使用 fetch API 发送加密后的数据包到选择的接口
	fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded; charset=utf-8"

			},
			body: params
		})
		.then(response => response.json())
		.then(data => {
			// 处理后端返回的 JSON 响应
			if (data.success) {
				alert("登录成功");
				window.location.href = "success.html";
			} else {
				alert("用户名或密码错误");
			}
		})
		.catch(error => {
			console.error("请求错误:", error);
		});

	// 关闭弹窗
	closeModal();
}

function sendEncryptedDataRSA(url) {
	// 公钥：在此处嵌入服务器提供的公钥
	const publicKey = `
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDRvA7giwinEkaTYllDYCkzujvi
NH+up0XAKXQot8RixKGpB7nr8AdidEvuo+wVCxZwDK3hlcRGrrqt0Gxqwc11btlM
DSj92Mr3xSaJcshZU8kfj325L8DRh9jpruphHBfh955ihvbednGAvOHOrz3Qy3Cb
ocDbsNeCwNpRxwjIdQIDAQAB
-----END PUBLIC KEY-----
  `;

	// 获取表单中的用户名和密码
	const username = document.getElementById("username").value;
	const password = document.getElementById("password").value;

	// 组织整个数据包
	const dataPacket = {
		username: username,
		password: password
	};

	// 将数据包转换为 JSON 字符串
	const dataString = JSON.stringify(dataPacket);

	// 初始化 JSEncrypt 并设置公钥
	const encryptor = new JSEncrypt();
	encryptor.setPublicKey(publicKey);

	// 对整个数据包进行加密
	const encryptedData = encryptor.encrypt(dataString);

	if (!encryptedData) {
		alert("加密失败，请检查公钥是否正确");
		return;
	}

	// 创建表单格式的请求体数据
	const formData = new URLSearchParams();
	formData.append('data', encryptedData); // 将加密后的数据作为一个字段传输

	// 发送加密后的数据到服务器
	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		body: formData.toString()
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			alert("登录成功");
			window.location.href = "success.html";
		} else {
			alert(data.error || "用户名或密码错误");
		}
	})
	.catch(error => console.error("请求错误:", error));

	// 关闭弹窗
	closeModal();
}

function sendDataAesRsa(url) {
	// 获取表单数据并转换为 JSON 字符串
	const formData = {
		username: document.getElementById("username")
			.value,
		password: document.getElementById("password")
			.value
	};
	const jsonData = JSON.stringify(formData);

	// 生成随机 AES 密钥和 IV
	const key = CryptoJS.lib.WordArray.random(16);
	const iv = CryptoJS.lib.WordArray.random(16);

	// 使用 AES 加密 JSON 数据
	const encryptedData = CryptoJS.AES.encrypt(jsonData, key, {
			iv: iv,
			mode: CryptoJS.mode.CBC,
			padding: CryptoJS.pad.Pkcs7
		})
		.toString();

	// 使用 RSA 公钥加密 AES 密钥
	const rsa = new JSEncrypt();
	rsa.setPublicKey(`-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDRvA7giwinEkaTYllDYCkzujvi
NH+up0XAKXQot8RixKGpB7nr8AdidEvuo+wVCxZwDK3hlcRGrrqt0Gxqwc11btlM
DSj92Mr3xSaJcshZU8kfj325L8DRh9jpruphHBfh955ihvbednGAvOHOrz3Qy3Cb
ocDbsNeCwNpRxwjIdQIDAQAB
-----END PUBLIC KEY-----`);

	const encryptedKey = rsa.encrypt(key.toString(CryptoJS.enc.Base64));
	const encryptedIv = rsa.encrypt(iv.toString(CryptoJS.enc.Base64));

	// 发送加密后的数据和加密的 AES 密钥与 IV 到服务器
	fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify({
				encryptedData: encryptedData,
				encryptedKey: encryptedKey,
				encryptedIv: encryptedIv
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert("登录成功");
				window.location.href = "success.html";
			} else {
				alert("用户名或密码错误");
			}
		})
		.catch(error => console.error("请求错误:", error));
	// 关闭弹窗
	closeModal();
}
async function fetchAndSendDataAes(url) {
	let aesKey, aesIv;

	try {
		// 获取 AES 密钥和 IV
		const response = await fetch("encrypt/server_generate_key.php");
		const data = await response.json();
		aesKey = CryptoJS.enc.Base64.parse(data.aes_key);
		aesIv = CryptoJS.enc.Base64.parse(data.aes_iv);
	} catch (error) {
		console.error("获取 AES 密钥失败:", error);
		alert("无法获取 AES 密钥，请刷新页面重试");
		return; // 如果获取密钥失败，停止后续操作
	}

	// 获取表单数据并转换为 JSON 字符串
	const formData = {
		username: document.getElementById("username")
			.value,
		password: document.getElementById("password")
			.value
	};
	const jsonData = JSON.stringify(formData);

	// 使用 AES 加密 JSON 数据
	const encryptedData = CryptoJS.AES.encrypt(jsonData, aesKey, {
			iv: aesIv,
			mode: CryptoJS.mode.CBC,
			padding: CryptoJS.pad.Pkcs7
		})
		.toString();

	// 发送加密后的数据包到服务器
	fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify({
				encryptedData: encryptedData
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert("登录成功");
				window.location.href = "success.html";
			} else {
				alert("用户名或密码错误");
			}
		})
		.catch(error => console.error("请求错误:", error));
	// 关闭弹窗
	closeModal();
}

function encryptAndSendDataDES(url) {
	// 获取用户名和密码
	const username = document.getElementById("username")
		.value;
	const password = document.getElementById("password")
		.value;

	// 密钥使用用户名拼接数字 '6' 补齐至 8 位
	const key = CryptoJS.enc.Utf8.parse(username.slice(0, 8)
		.padEnd(8, '6'));

	// IV 使用数字 '9999' 拼接用户名补齐至 8 位
	const iv = CryptoJS.enc.Utf8.parse('9999' + username.slice(0, 4)
		.padEnd(4, '9'));

	// 只加密密码，不加中括号
	const encryptedPassword = CryptoJS.DES.encrypt(password, key, {
		iv: iv,
		mode: CryptoJS.mode.CBC,
		padding: CryptoJS.pad.Pkcs7
	});

	// 将加密后的数据转换为 Hex 格式
	const encryptedHex = encryptedPassword.ciphertext.toString(CryptoJS.enc.Hex);

	// 使用 fetch API 发送包含用户名和加密密码的数据包到服务器
	fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify({
				username: username,
				password: encryptedHex
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert("登录成功");
				window.location.href = "success.html";
			} else {
				alert("用户名或密码错误");
			}
		})
		.catch(error => console.error("请求错误:", error));
	// 关闭弹窗
	closeModal();
}

function sendDataWithNonce(url) {
	// 获取用户名和密码
	const username = document.getElementById("username")
		.value;
	const password = document.getElementById("password")
		.value;

	// 生成随机数（nonce）和时间戳
	const nonce = Math.random()
		.toString(36)
		.substring(2); // 简单随机字符串
	const timestamp = Math.floor(Date.now() / 1000); // 当前时间戳（秒）

	// 共享密钥，用于生成签名。此密钥应为双方约定的共享密钥
	const secretKey = "be56e057f20f883e";

	// 生成签名内容
	const dataToSign = username + password + nonce + timestamp;
	const signature = CryptoJS.HmacSHA256(dataToSign, secretKey)
		.toString(CryptoJS.enc.Hex);

	// 使用 fetch API 发送包含用户名、密码、随机数、时间戳和签名的数据包到服务器
	fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify({
				username: username,
				password: password,
				nonce: nonce,
				timestamp: timestamp,
				signature: signature
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert("登录成功");
				window.location.href = "success.html";
			} else {
				alert(data.error || "用户名或密码错误");
			}
		})
		.catch(error => console.error("请求错误:", error));
	// 关闭弹窗
	closeModal();
}

function sendDataWithSignatureRsa(url) {
	// 公钥：在此处嵌入服务器提供的公钥
	const publicKey = `
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDRvA7giwinEkaTYllDYCkzujvi
NH+up0XAKXQot8RixKGpB7nr8AdidEvuo+wVCxZwDK3hlcRGrrqt0Gxqwc11btlM
DSj92Mr3xSaJcshZU8kfj325L8DRh9jpruphHBfh955ihvbednGAvOHOrz3Qy3Cb
ocDbsNeCwNpRxwjIdQIDAQAB
-----END PUBLIC KEY-----
  `;

	// 获取表单中的用户名和密码
	const username = document.getElementById("username")
		.value;
	const password = document.getElementById("password")
		.value;

	// 生成随机数（nonce）和时间戳
	const nonce = Math.random()
		.toString(36)
		.substring(2); // 简单随机字符串
	const timestamp = Math.floor(Date.now() / 1000); // 当前时间戳（秒）

	// 发送数据到服务器并验证签名
	async function sendRequest() {
		// 创建发送的数据包
		const dataToSend = {
			username: username,
			password: password,
			nonce: nonce,
			timestamp: timestamp
		};

		try {
			// 发送 POST 请求
			const response = await fetch(url, {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify(dataToSend)
			});

			// 解析 JSON 响应
			const responseData = await response.json();

			// 检查服务器返回的成功状态和签名
			if (responseData.success && responseData.signature) {
				// 生成待验证的数据字符串
				const dataToVerify = username + password + nonce + timestamp;

				// 验证签名
				const signatureValid = verifySignature(dataToVerify, responseData.signature, publicKey);

				if (signatureValid) {
					alert("签名验证成功，登录成功");
					window.location.href = "success.html";
				} else {
					alert("签名验证失败，数据可能被篡改");
				}
			} else {
				alert(responseData.error || "用户名或密码错误");
			}
		} catch (error) {
			console.error("请求错误:", error);
			alert("请求失败，请检查网络连接");
		}
	}

	// 调用函数发送请求
	sendRequest();

	// 签名验证函数，使用服务器提供的公钥
	function verifySignature(data, signature, publicKey) {
		const verifier = new JSEncrypt();
		verifier.setPublicKey(publicKey);
		return verifier.verify(data, signature, CryptoJS.SHA256);
	}
	// 关闭弹窗
	closeModal();
}
// 生成带时间戳的请求
function generateRequestData() {
	const username = document.getElementById("username")
		.value;
	const password = document.getElementById("password")
		.value;
	const timestamp = Date.now(); // 获取当前时间戳，单位：毫秒

	const dataToSend = {
		username: username,
		password: password,
		timestamp: timestamp // 发送时间戳，用来防止重放攻击
	};

	return dataToSend;
}

// 发送请求
function sendLoginRequest(url) {
	const dataToSend = generateRequestData();

	fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/json; charset=utf-8"
			},
			body: JSON.stringify(dataToSend)
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert("登录成功");
				window.location.href = "success.html";
			} else {
				alert(data.error || "用户名或密码错误");
			}
		})
		.catch(error => console.error("请求错误:", error));
	// 关闭弹窗
	closeModal();
}
// 关闭弹窗
function closeModal() {
	document.getElementById("modal")
		.style.display = "none";
}
