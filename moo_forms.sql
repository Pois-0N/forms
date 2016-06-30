CREATE TABLE `moo_admin` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) character set gbk NOT NULL,
  `pass` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


INSERT INTO `moo_admin` (`id`, `name`, `pass`) VALUES 
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

CREATE TABLE `moo_form` (
  `fid` int(11) NOT NULL auto_increment COMMENT '��ID',
  `fname` char(100) NOT NULL COMMENT '������',
  `fmsg` mediumtext NOT NULL COMMENT '��˵��',
  `addtime` int(10) NOT NULL COMMENT '���ʱ��',
  `display` int(1) NOT NULL default '1',
  PRIMARY KEY  (`fid`)
) TYPE=MyISAM;

INSERT INTO `moo_form` (`fid`, `fname`, `fmsg`, `addtime`, `display`) VALUES 
(1, '��ʾ��', '�������ʾ�������û��ο���', 1209817185, 1);

CREATE TABLE `moo_form_data` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(20) NOT NULL COMMENT '������ID',
  `content` mediumtext character set gbk NOT NULL COMMENT '����',
  `addtime` int(10) NOT NULL COMMENT '���ʱ��',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `moo_form_type` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(20) NOT NULL COMMENT '������ID',
  `orderid` int(10) NOT NULL COMMENT '����',
  `type` varchar(20) NOT NULL COMMENT '����',
  `title` varchar(40) NOT NULL COMMENT '����',
  `msg` varchar(255) NOT NULL COMMENT '˵��',
  `options` mediumtext NOT NULL COMMENT 'ѡ��',
  `defaultvalue` mediumtext NOT NULL COMMENT 'Ĭ��ֵ',
  `ismust` int(1) NOT NULL default '0' COMMENT '������������',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `moo_form_type` (`id`, `fid`, `orderid`, `type`, `title`, `msg`, `options`, `defaultvalue`, `ismust`) VALUES 
(6, 1, 255, 'textarea', '����', '', '', '', 1),
(5, 1, 6, 'radio', '����״��', '', '�ѻ�\r\nδ��', '', 0),
(4, 1, 4, 'text', '��ַ', '', '', '', 0),
(3, 1, 3, 'checkbox', '����', '��ѡ�����İ���', '��Ϸ\r\n����\r\n����\r\n����', '', 0),
(2, 1, 2, 'text', 'E_mail', '���������ĳ���E_mail��ַ', '', '', 1),
(1, 1, 1, 'text', '����', '��������������', '', '', 1);
