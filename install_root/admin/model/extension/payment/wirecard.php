<?php 
class ModelExtensionPaymentwirecard extends Model {
	public function install() {
				$this->load->model('setting/setting');
		$this->db->query( 'CREATE TABLE IF NOT EXISTS `'.DB_PREFIX.'wirecard_payment` (
		  `order_id` int(10) unsigned NOT NULL,
		  `customer_id` int(10) unsigned NOT NULL,
		  `wirecard_id` varchar(64) NULL,
		  `bank` varchar(12) NULL,
		  `amount` decimal(10,4) NOT NULL,
		   `amount_paid` decimal(10,4) NOT NULL,
		   `installment` int(2) unsigned NOT NULL DEFAULT 1,
		  `cardholdername` varchar(60) NULL,
		  `cardnumber` varchar(25) NULL,
		  `cardexpdate` varchar(8) NULL,
		  `createddate` datetime NOT NULL,
		  `ipaddress` varchar(16) NULL,
		  `status_code` tinyint(1) DEFAULT 1,
		  `result_code` varchar(60) NULL,
		  `result_message` varchar(256) NULL,
		  `mode` varchar(16) NULL,
		  `shared_payment_url` varchar(256) NULL,
			  KEY `order_id` (`order_id`),
			KEY `customer_id` (`customer_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
		$this->config->set('wirecard_3d_mode', 'off');
		return true;
	}
}
