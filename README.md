# cosUploadV5
针对腾讯云cos v5更新

在Plugin.php 151行左右可以修改上传的默认目录：
本插件默认上传目录：$filePath = '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';

typecho默认上传目录：$filePath = '/' . 'usr' . '/' . 'uploads' . '/' . date('Y') . '/' . date('m') . '/';

修改$filePath变量即可
