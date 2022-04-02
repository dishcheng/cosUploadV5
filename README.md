# cosUploadV5
针对腾讯云cos v5更新

在Plugin.php 151行左右可以修改上传的默认目录：
本插件默认上传目录：$filePath = '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';

typecho默认上传目录：$filePath = '/' . 'usr' . '/' . 'uploads' . '/' . date('Y') . '/' . date('m') . '/';

修改$filePath变量即可


[typecho<=1.1 下载](https://github.com/dishCheng/cosUploadV5)


[typecho==1.2 下载 ](https://github.com/dishcheng/cosUploadV5/tree/typecho_1.2)