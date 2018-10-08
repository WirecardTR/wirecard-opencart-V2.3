<?php
class ModelPaymentWirecard extends Model {
	public function getMethod($address, $total) {

			$method_data = array(
				'code'       => 'wirecard',
				'title'      => 'Kredi kartı ile öde',
				'terms'      => '',
				'sort_order' => 1
			);


		return $method_data;
	}
}