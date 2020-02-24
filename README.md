# SplitArchivePage
typecho 内容分页插件：当你的文章内容很长时，可以考虑用此插件来给文章进行简单的分页

原作者 膘叔
http://neatstudio.com/show-1333-1.shtml

更新日志：
- 0.1.3 修正了内容页中如果没有插入分页符内容不能显示的BUG
- 0.1.4 修正了Rewrite规则下，还会自动加上index.php的BUG，目前在Rewrite规则下去除了index.php
- 0.1.5 原有的程序只支持一个GET变量，现在已修正，只要是GET变量都支持
- 0.1.6 修复了typecho1.1后无法识别分页标记问题 by Noisky，优化了显示样式