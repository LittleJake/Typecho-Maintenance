<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Maintenance Mode
 *
 * @package Maintenance
 * @author LittleJake
 * @version 1.0.0
 * @link https://blog.littlejake.net
 */
class Maintenance_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     */
    public static function activate()
    {
         Typecho_Plugin::factory('Widget_Archive')->beforeRender = array('Maintenance_Plugin', 'render');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $switch = new Typecho_Widget_Helper_Form_Element_Checkbox('switch'
            , ['on' => _t("开启")], NULL, _t('是否开启维护模式'));
        $token = new Typecho_Widget_Helper_Form_Element_Text('token', NULL,
            Typecho_Common::randString(32), _t('Token参数'), _t('用于访问Maintenance模式开发'));
        $form->addInput($switch);
        $form->addInput($token);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     * @throws Typecho_Exception
     */
    public static function render()
    {
        if (!Typecho_Widget::widget('Widget_Options')->plugin('Maintenance')
            || !Typecho_Widget::widget('Widget_Options')->plugin('Maintenance')->switch) {
            return;
        }
        //渲染503页面
        if(in_array('on', Typecho_Widget::widget('Widget_Options')
            ->plugin('Maintenance')->switch)
            && $_REQUEST['token'] != Typecho_Widget::widget('Widget_Options')
                ->plugin('Maintenance')->token){

            $img_url = Helper::options()->pluginUrl . '/Maintenance/img/Maintenance.png';
            echo
            <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="initial-scale=1, minimum-scale=1, width=device-width">
  <title>网站维护中 Maintenance</title>
  <style>
    html{background: #fff;color: #222;padding: 15px;}  body {margin: 7% auto 0;max-width: 390px;min-height: 180px;padding: 30px 0 15px;}
    * > body{background: url("{$img_url}") 0 5px no-repeat;padding-left: 300px;}  p{margin: 11px 0 22px;overflow: hidden;}
  </style>
</head>
<body>
<p>HTTP 503</p>
<p>网站正在维护中</p>
<p>Under maintenance. we'll be back in few minutes.</p>
</body>
</html>
EOF;
            Typecho_Response::setStatus(503);
            exit;
        }
    }
}
