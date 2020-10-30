<?php
/**
 * cosV5--for Typecho 使用cosV5版本sdk
 *
 * @package cosUploadV5
 * @author 菜菜子
 * @version 3.0
 * @link https://www.cc430.cn/index.php/archives/458/
 * @date 2018.4.1
 */
require(__DIR__ . DIRECTORY_SEPARATOR . 'cos-autoloader.php');

class cosUploadV5_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        //上传
        Typecho_Plugin::factory('Widget_Upload')->uploadHandle = array('cosUploadV5_Plugin', 'uploadHandle');
        //修改
        Typecho_Plugin::factory('Widget_Upload')->modifyHandle = array('cosUploadV5_Plugin', 'modifyHandle');
        //删除
        Typecho_Plugin::factory('Widget_Upload')->deleteHandle = array('cosUploadV5_Plugin', 'deleteHandle');
        //路径参数处理
        Typecho_Plugin::factory('Widget_Upload')->attachmentHandle = array('cosUploadV5_Plugin', 'attachmentHandle');
        //文件内容数据
        Typecho_Plugin::factory('Widget_Upload')->attachmentDataHandle = array('cosUploadV5_Plugin', 'attachmentDataHandle');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
    }

    public static function init($options)
    {
        return new Qcloud\Cos\Client(array('region' => $options->region,
            'credentials' => array(
                'appId' => $options->appid,
                'secretId' => $options->ak,
                'secretKey' => $options->sk)));
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $appid = new Typecho_Widget_Helper_Form_Element_Text('appid',
            null, '',
            _t('APPID：'),
            _t('<a href="https://console.qcloud.com/cam/capi" target="_blank">获取APPID</a>'));
        $form->addInput($appid->addRule('required', _t('APPID 不能为空！')));

        $ak = new Typecho_Widget_Helper_Form_Element_Text('ak',
            NULL, '',
            _t('SecretId：'),
            _t('<a href="https://console.qcloud.com/cam/capi" target="_blank">获取SecretId</a>'));
        $form->addInput($ak->addRule('required', _t('SecretId不能为空！')));

        $sk = new Typecho_Widget_Helper_Form_Element_Text('sk',
            NULL, '',
            _t('SecretKey：'),
            _t('<a href="https://console.qcloud.com/cam/capi" target="_blank">获取SecretKey</a>'));
        $form->addInput($sk->addRule('required', _t('SecretKey不能为空！')));

        $region = new Typecho_Widget_Helper_Form_Element_Radio('region',
            array(
                'ap-beijing-1' => _t('北京一区（华北）'),
                'ap-beijing' => _t('北京'),
                'ap-shanghai' => _t('上海（华东）'),
                'ap-guangzhou' => _t('广州（华南）'),
                'ap-chengdu' => _t('成都（西南）'),
                'ap-chongqing' => _t('重庆'),
                'ap-singapore' => _t('新加坡'),
                'ap-hongkong' => _t('香港'),
                'na-toronto' => _t('多伦多'),
                'eu-frankfurt' => _t('法兰克福'),
                'ap-mumbai' => _t('孟买'),
                'ap-seoul' => _t('首尔'),
                'na-siliconvalley' => _t('硅谷'),
                'na-ashburn' => _t('弗吉尼亚'),
                'ap-shenzhen-fsi' => _t('深圳金融'),
                'ap-shanghai-fsi' => _t('上海金融'),
                'ap-beijing-fsi' => _t('北京金融'),
                'ap-nanjing' => _t('南京'),
            ),
            'ap-guangzhou',
            _t('选择bucket节点')
        );
        $form->addInput($region->addRule('required', _t('bucket节点不能为空！')));


        $bucketName = new Typecho_Widget_Helper_Form_Element_Text('bucket',
            NULL, 'bucketName',
            _t('Bucket名称：'),
            _t('例如bucket-1'));
        $form->addInput($bucketName->addRule('required', _t('Bucket名称不能为空！')));


        $domain = new Typecho_Widget_Helper_Form_Element_Text('domain',
            NULL, 'http://',
            _t('使用的域名,必填,请带上http://或https://，可使用默认域名或自定义域名'),
            _t('在bucket中的域名管理<br>默认域名形如：http://bucket-1-1252063625.cos.ap-chengdu.myqcloud.com<br>自定义域名形如：https://cos.cc430.cn'));
        $form->addInput($domain->addRule('required', _t('cos域名不能为空！')));
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 上传文件处理函数
     *
     * @access public
     * @param array $file 上传的文件
     * @return mixed
     */
    public static function uploadHandle($file)
    {
        if (empty($file['name'])) return false;
        //获取扩展名
        $ext = self::getSafeName($file['name']);
        //判定是否是允许的文件类型
        if (!Widget_Upload::checkFileType($ext)) return false;

        //获取文件名
        $filePath = '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        $fileName = time() . '.' . $ext;
        //cos上传文件的路径+名称
        $newPath=$filePath.$fileName;
        //如果没有临时文件，则退出
        if (!isset($file['tmp_name'])) return false;
        //获取插件参数
        $options = Typecho_Widget::widget('Widget_Options')->plugin('cosUploadV5');

        //初始化cos
        $cos_object = self::init($options);
        $srcPath = $file['tmp_name'];
        $handle = fopen($srcPath, "r");
        $contents = fread($handle, $file['size']);//获取二进制数据流
        fclose($handle);
        $cos_object->upload($options->bucket, $newPath, $contents);

        return array(
            'name' => $file['name'],
            'path' => $newPath,
            'size' => $file['size'],
            'type' => $ext,
            'mime' => @Typecho_Common::mimeContentType(rtrim($options->domain, '/') . '/' .  $newPath),
        );
    }

    /*
     * 初始化bucket位置
     */
    public static function region($option)
    {
        $region = $option->region;
        return $region;
    }


    /**
     * 文件删除
     *
     * @access public
     * @param array $content 当前文件信息
     * @return mixed
     */
    public static function deleteHandle($content)
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('cosUploadV5');
        $cos_object = self::init($options);

        $err = $cos_object->deleteObject(array(
            'Bucket' => $options->bucket,
            'Key' => $content['attachment']->path)
        );
        return !$err;
    }

    /**
     * 获取实际文件数据
     *
     * @access public
     * @param array $content
     * @return string
     */
    public static function attachmentDataHandle($content)
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('cosUploadV5');
        $cos_object = self::init($options);
        list($ret, $err) = $cos_object->stat($options->bucket, $content['attachment']);
        return $err === null ? $ret : false;
    }

    /**
     * 获取实际文件绝对访问路径
     *
     * @access public
     * @param array $content 文件相关信息
     * @return string
     */
    public static function attachmentHandle(array $content)
    {
        $domain = Typecho_Widget::widget('Widget_Options')->plugin('cosUploadV5')->domain;
        $tmp = preg_match('/http(s)?:\/\/[\w\d\.\-\/]+$/is', $domain);    //粗略验证域名
        if (!$tmp) return false;
        return Typecho_Common::url($content['attachment']->path, $domain);
    }


    /**
     * 获取安全的文件名
     *
     * @param string $name
     * @static
     * @access private
     * @return string
     */
    private static function getSafeName(&$name)
    {
        $name = str_replace(array('"', '<', '>'), '', $name);
        $name = str_replace('\\', '/', $name);
        $name = false === strpos($name, '/') ? ('a' . $name) : str_replace('/', '/a', $name);
        $info = pathinfo($name);
        $name = substr($info['basename'], 1);
        return isset($info['extension']) ? strtolower($info['extension']) : '';
    }
}
