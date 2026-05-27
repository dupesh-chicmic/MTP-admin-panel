<?php
$db=array();

$tableSql=<<<EOM
CREATE TABLE IF NOT EXISTS `qpay_method` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) NOT NULL) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `qpay_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
`method_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `timestamp` DATETIME,
`status` tinyint,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `qpay_realex_request` (
  `order_id` int(11) NOT NULL,
  `merchant_id` varchar(50) NOT NULL,
  `account` varchar(30) DEFAULT NULL,
  `currency` char(3) NOT NULL,
  `amount` int(11) NOT NULL,
  `timestamp` char(14) NOT NULL,
  `sha1hash` char(40) NOT NULL,
  `auto_settle_flag` bit(1) DEFAULT NULL,
  `comment1` varchar(255) NOT NULL,
  `comment2` varchar(255) NOT NULL,
  `return_tss` bit(1) DEFAULT NULL,
  `shipping_code` varchar(30) DEFAULT NULL,
  `shipping_co` varchar(50) DEFAULT NULL,
  `billing_cod` varchar(50) DEFAULT NULL,
  `cust_num` varchar(50) DEFAULT NULL,
  `var_ref` varchar(50) DEFAULT NULL,
  `prod_id` varchar(50) DEFAULT NULL,
  `hpp_lang` char(2) DEFAULT NULL,
  `merchant_response_url` varchar(255) DEFAULT NULL,
  `card_payment_button` varchar(25) DEFAULT NULL,
  `authcode` varchar(30) DEFAULT NULL,
  `result` varchar(10) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `cvnresult` char(3) DEFAULT NULL,
  `pasref` varchar(30) DEFAULT NULL,
  `batchid` int(11) DEFAULT NULL,
  `eci` varchar(30) DEFAULT NULL,
  `cavv` varchar(30) DEFAULT NULL,
  `xid` varchar(30) DEFAULT NULL,
  `tss` varchar(30) DEFAULT NULL,
  `tss_idnum` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(20) NOT NULL,
  `total_gross` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL,
`bp_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `no` (`no`),
KEY `bp_id` (`bp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `invoice_qpay_request` (
  `invoice_id` int(11) NOT NULL,
  `qpay_request_id` int(11) NOT NULL,
  UNIQUE KEY `invoice_id_2` (`invoice_id`,`qpay_request_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `qpay_request_id` (`qpay_request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE  `qpay_realex_request` ADD FOREIGN KEY (  `order_id` ) REFERENCES `qpay_request` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT ;
ALTER TABLE  `qpay_request` ADD INDEX (  `method_id` ) COMMENT  '';
ALTER TABLE  `qpay_request` ADD FOREIGN KEY (  `method_id` ) REFERENCES `qpay_method` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;
ALTER TABLE `invoice_qpay_request` ADD CONSTRAINT `invoice_qpay_request_ibfk_2` FOREIGN KEY (`qpay_request_id`) REFERENCES `qpay_request` (`id`), ADD CONSTRAINT `invoice_qpay_request_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`);

INSERT INTO `qpay_method` (`id` ,`name`) VALUES (NULL ,  'realex');
EOM;

$db['qpay_realex_request']=$tableSql;

return $db;