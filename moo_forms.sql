CREATE TABLE `moo_admin` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) character set gbk NOT NULL,
  `pass` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


INSERT INTO `moo_admin` (`id`, `name`, `pass`) VALUES 
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

CREATE TABLE `moo_form` (
  `fid` int(11) NOT NULL auto_increment COMMENT '表单ID',
  `fname` char(100) NOT NULL COMMENT '表单名称',
  `fmsg` mediumtext NOT NULL COMMENT '表单说明',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `display` int(1) NOT NULL default '1',
  PRIMARY KEY  (`fid`)
) TYPE=MyISAM;

INSERT INTO `moo_form` (`fid`, `fname`, `fmsg`, `addtime`, `display`) VALUES 
(1, '演示表单', '这个是演示表单，供用户参考！', 1209817185, 1);

CREATE TABLE `moo_form_data` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(20) NOT NULL COMMENT '所属表单ID',
  `content` mediumtext character set gbk NOT NULL COMMENT '内容',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `moo_form_type` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(20) NOT NULL COMMENT '所属表单ID',
  `orderid` int(10) NOT NULL COMMENT '排序',
  `type` varchar(20) NOT NULL COMMENT '类型',
  `title` varchar(40) NOT NULL COMMENT '标题',
  `msg` varchar(255) NOT NULL COMMENT '说明',
  `options` mediumtext NOT NULL COMMENT '选项',
  `defaultvalue` mediumtext NOT NULL COMMENT '默认值',
  `ismust` int(1) NOT NULL default '0' COMMENT '表单内容项限制',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `moo_form_type` (`id`, `fid`, `orderid`, `type`, `title`, `msg`, `options`, `defaultvalue`, `ismust`) VALUES 
(6, 1, 255, 'textarea', '内容', '', '', '', 1),
(5, 1, 6, 'radio', '婚姻状况', '', '已婚\r\n未婚', '', 0),
(4, 1, 4, 'text', '地址', '', '', '', 0),
(3, 1, 3, 'checkbox', '爱好', '请选择您的爱好', '游戏\r\n音乐\r\n看书\r\n旅游', '', 0),
(2, 1, 2, 'text', 'E_mail', '请输入您的常用E_mail地址', '', '', 1),
(1, 1, 1, 'text', '姓名', '请输入您的姓名', '', '', 1);
