<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 当你的文章内容很长时，可以考虑用此插件来给文章进行简单的分页，在需要分页的地方插入page（尖括号包住）即可
 *
 * @package SplitArchivePage
 * @author  Noisky && gouki
 * @version 0.1.6
 * @link http://ffis.me
 * @gouki http://www.neatstudio.com/
 *
 * 更新日志：
 * 0.1.3 修正了内容页中如果没有插入分页符内容不能显示的BUG
 * 0.1.4 修正了Rewrite规则下，还会自动加上index.php的BUG，目前在Rewrite规则下去除了index.php
 * 0.1.5 原有的程序只支持一个GET变量，现在已修正，只要是GET变量都支持
 * 0.1.6 修复了typecho1.1后无法识别分页标记问题 by Noisky
 */
class SplitArchivePage_Plugin implements Typecho_Plugin_Interface
{
    protected static $splitWord = '<page>';
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate(){
        //原本考虑主动在post和page页插入分页符的，经友情提示，这些可以去除。
        //因为如果不这样，我要考虑很多东西，比如richEdit编辑器，但这种编辑器太多了。所以直接根据友情提示而放弃主动插入
        //如果你不用richEdit，这两行注释可以打开。
//        Typecho_Plugin::factory('admin/write-post.php')->content = array('SplitArchivePage_Plugin', 'render');
//        Typecho_Plugin::factory('admin/write-page.php')->content = array('SplitArchivePage_Plugin', 'render');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('SplitArchivePage_Plugin', 'parse');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @static
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){
        //设置分页标记
        $name = new Typecho_Widget_Helper_Form_Element_Text('word', NULL, '<page>', _t('分页标记'));
        $form->addInput($name);
        $name = new Typecho_Widget_Helper_Form_Element_Text('prev', NULL, '上一页', _t('上一页显示'));
        $form->addInput($name);
        $name = new Typecho_Widget_Helper_Form_Element_Text('next', NULL, '下一页', _t('下一页显示'));
        $form->addInput($name);

    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
        
    }

    /**
     * 默认编辑器插入分页符功能。
     * @access public
     * @return void
     */
    public static function render(){
		        ?><style> li#wmd-insplit-button{font-size: 20px;line-height: 20px;height: 20px;width: 20px;}</style>
		<?php
        $splitword = Typecho_Widget::widget('Widget_Options')->plugin('SplitArchivePage')->word;
        if(!$splitword){
            $splitword = self::$splitWord;
        }
        echo "<input type='button' name='InsertSplitPage' value='"._t('插入分页符')."' onclick='document.getElementById(\"text\").value += \"{$splitword}\";'/>";
    }

    /**
     * 插件实现方法（写的很挫，半夜写的，先实现以后再改。）
     *
     * @access public
     * @param string $text string
     * @param object $widget
     * @param string $lastResult
     * @return void
     */
    public static function parse($text, $widget, $lastResult)
    {
        $text = empty($lastResult) ? $text : $lastResult;
        $content = $pagebar = '';
        if ($widget instanceof Widget_Archive ) {
            $splitword = Typecho_Widget::widget('Widget_Options')->plugin('SplitArchivePage')->word;
            if(!$splitword){
                $splitword = self::$splitWord;
            }
            if(Typecho_Router::$current == 'post'){
                $content = $text;
                if( strpos( $text , $splitword) !== false){
                    $contents = explode($splitword , $text );
                    $page = isset($_GET['page'])?intval($_GET['page']):1;
                    $content = $contents[$page-1];
                    $request = Typecho_Request::getInstance();
                    $_GET['page'] = '{page}';
                    $pagebar = self::setPageBar(count($contents),$page,$request->getPathinfo()."?".  http_build_query($_GET));
                }
            }else{
                $content = str_replace($splitword, '', $text);
                $pagebar = '';
            }
        }
        $text = $content.$pagebar;
        return $text;
    }

    private static function setPageBar($pageTotals,$page,$pageTemplate)
    {
		$selfOptions = Typecho_Widget::widget('Widget_Options')->plugin('SplitArchivePage');
        $isRewrite = Typecho_Widget::widget('Widget_Options')->rewrite;
        $siteUrl = Typecho_Widget::widget('Widget_Options')->siteUrl;
        $pageTemplate = ($isRewrite ? rtrim($siteUrl, '/') : $siteUrl."index.php") . $pageTemplate;
        $prevWord = isSet( $selfOptions->prev ) ? $selfOptions->prev : 'PREV';
        $nextWord = isSet( $selfOptions->next ) ? $selfOptions->next : 'NEXT';
        $splitPage = 3;
        $pageHolder = array('{page}', '%7Bpage%7D');
        if ($pageTotals < 1) {
            return;
        }
        $pageBar = "<link rel='stylesheet' media='screen' type='text/css' href='".Helper::options()->pluginUrl . "/SplitArchivePage/pagebar.css"."' />";
        $pageBar .= '<div class="archives_page_bar">';
        //输出上一页
        if ($page > 1) {
            $pageBar .= '<a class="prev" href="' . str_replace($pageHolder, $page - 1, $pageTemplate) . '">'
            . $prevWord . '</a>';
        }
        for ($i = 1; $i <= $pageTotals; $i ++) {
           $pageBar .= '<a href="' .
                str_replace($pageHolder, $i, $pageTemplate) . '" ' . ($i != $page ? '' : ' class="sel"') . '>'
                . $i . '</a>';
        }
        if ($page < $pageTotals) {
            $pageBar .= '<a class="next" href="' . str_replace($pageHolder, $page + 1, $pageTemplate)
             . '">' . $nextWord . '</a>';
        }
        $pageBar .='</div>';
        return $pageBar;
    }

}
?>
