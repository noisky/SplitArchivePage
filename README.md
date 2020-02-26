# SplitArchivePage
Typecho 文章内容分页插件：当你的文章内容很长时，可以考虑用此插件来给文章进行简单的分页

原作者 膘叔 ：http://neatstudio.com/show-1333-1.shtml

分页效果：https://ffis.me/experience/1815.html

## 安装方法：

Download ZIP, 解压，将 SplitArchivePage-master 重命名为 SplitArchivePage ，之后上传到你博客中的 /usr/plugins 目录，在后台启用即可

或者直接使用 git clone 命令
```
# 进入博客 /usr/plugins 目录下
cd /usr/plugins
git clone https://github.com/noisky/SplitArchivePage.git SplitArchivePage
```

## 使用方法：

在需要分页的地方加入 `<page>` 标识符即可

## 更新日志：
- 0.1.3 修正了内容页中如果没有插入分页符内容不能显示的 BUG
- 0.1.4 修正了 Rewrite 规则下，还会自动加上 index.php 的BUG，目前在 Rewrite 规则下去除了 index.php
- 0.1.5 原有的程序只支持一个 GET 变量，现在已修正，只要是 GET 变量都支持
- 0.1.6 修复了 Typecho1.1 后无法识别分页标记问题，优化了显示样式 by Noisky