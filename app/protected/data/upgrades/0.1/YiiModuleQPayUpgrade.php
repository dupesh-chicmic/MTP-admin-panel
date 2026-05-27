<?php
class YiiModuleQPayUpgrade extends SystemUpgrade
{
	public $requiredAppComponents=array('db', 'cache', 'settings');
	public $requiredModules=array('qpay');

	public function init()
	{
		$this->version='0.1';
		parent::init();
	}

	public function upgradeRealex()
	{
		$db=$this->getDb();

		if(!$this->tableExists('qpay_realex_request'))
		{
			$dbSql=$this->getDbSql();
			$dbSql=$dbSql['qpay_realex_request'];
			$this->executeSql($dbSql);
		}

		$this->updateSettings(
			array('qpay.realex'=>array(
				'merchant ID'=>'motortradepublishers',
				'hosted payment page URL'=>'https://hpp.realexpayments.com/pay',
				'test hosted payment page URL'=>'https://hpp.sandbox.realexpayments.com/pay',
				'shared secret'=>'dhFphqVRPa',
				'default currency'=>'EUR',
				'account'=>'internet',
				'merchant response URL'=>'http://www.mtp.ie/test/index.php?r=qpay/qpayrealex/qpayrealex/response',
				'test mode'=>true,
			)), true
		);

		if(!CmsPage::model()->findByAttributes(array('name'=>'Pay Subscription')))
			$this->executeSql("INSERT INTO `en_cms_page` (`parent_id`, `url`, `name`, `title`, `header`, `link_name`, `keywords`, `function`, `layout`, `template`, `seo_visible`, `seo_unvisible`, `param_1`, `param_2`, `txt`, `button`, `order`, `editable`, `display`, `description`, `deletable`)
			VALUES ('3', 'pay_invoice', 'Pay Subscription', 'Pay Subscription', 'Pay Subscription', 'Pay Subscription', 'Pay Subscription', '4', '7', NULL, NULL, NULL, 'index.php?r=realex/payInvoice', NULL, NULL, NULL, '9', '0', '1', NULL, '0')");
	}
}