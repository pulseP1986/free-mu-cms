<?php

/**
*		MODULE TẠO ĐƯỜNG LINK THANH TOÁN QUA NGÂNLƯỢNG VÀ KIỂM TRA ĐƯỜNG LINK KẾT QUẢ THANH TOÁN TRẢ VỀ TỪ NGÂNLƯỢNG.VN. 
*		Mã checksum là: một tham số được tạo bởi thuật toán MD5 các tham số giao tiếp giữa NgânLượng.vn & website bán hàng 
* 		và một chuỗi ký tự gọi là mật khẩu giao tiếp.
*		Người dùng không thể tự động sửa được giá trị tham số giao tiếp giữa website của bạn và NgânLượng.vn nếu như không biết chính xác tham số bạn đã lưu và mật khẩu giao tiếp là gì. 
**/

class NL_Checkout 
{
	// Địa chỉ thanh toán hoá đơn của NgânLượng.vn
	public $nganluong_url = 'https://www.nganluong.vn/checkout.php';	
	// Mã website của bạn đăng ký trong chức năng tích hợp thanh toán của NgânLượng.vn.
	public $merchant_site_code = '275'; //100001 chỉ là ví dụ, bạn hãy thay bằng mã của bạn
	// Mật khẩu giao tiếp giữa website của bạn và NgânLượng.vn.
	public $secure_pass= '123456'; //d685739bf1 chỉ là ví dụ, bạn hãy thay bằng mật khẩu của bạn
	// Nếu bạn thay đổi mật khẩu giao tiếp trong quản trị website của chức năng tích hợp thanh toán trên NgânLượng.vn, vui lòng update lại mật khẩu này trên website của bạn
	public $affiliate_code = ''; //Mã đối tác tham gia chương trình liên kết của NgânLượng.vn
		
	/**
	 * HÀM TẠO ĐƯỜNG LINK THANH TOÁN QUA NGÂNLƯỢNG.VN VỚI THAM SỐ MỞ RỘNG
	 *
	 * @param string $return_url: Đường link dùng để cập nhật tình trạng hoá đơn tại website của bạn khi người mua thanh toán thành công tại NgânLượng.vn
	 * @param string $receiver: Địa chỉ Email chính của tài khoản NgânLượng.vn của người bán dùng nhận tiền bán hàng
	 * @param string $transaction_info: Tham số bổ sung, bạn có thể dùng để lưu các tham số tuỳ ý để cập nhật thông tin khi NgânLượng.vn trả kết quả về
	 * @param string $order_code: Mã hoá đơn hoặc tên sản phẩm
	 * @param int $price: Tổng tiền hoá đơn/sản phẩm, chưa kể phí vận chuyển, giảm giá, thuế.
	 * @param string $currency: Loại tiền tệ, nhận một trong các giá trị 'vnd', 'usd'. Mặc định đồng tiền thanh toán là 'vnd'
	 * @param int $quantity: Số lượng sản phẩm
	 * @param int $tax: Thuế
	 * @param int $discount: Giảm giá
	 * @param int $fee_cal: Nhận giá trị 0 hoặc 1. Do trên hệ thống NgânLượng.vn cho phép chủ tài khoản cấu hình cho nhập/thay đổi phí lúc thanh toán hay không. Nếu website của bạn đã có phí vận chuyển và không cho sửa thì đặt tham số này = 0
	 * @param int $fee_shipping: Phí vận chuyển
	 * @param string $order_description: Mô tả về sản phẩm, đơn hàng
	 * @param string $buyer_info: Thông tin người mua 
	 * @param string $affiliate_code: Mã đối tác tham gia chương trình liên kết của NgânLượng.vn
	 * @return string
	 */
	public function buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency = 'vnd', $quantity = 1, $tax = 0, $discount = 0, $fee_cal = 0, $fee_shipping = 0, $order_description = '', $buyer_info = '', $affiliate_code = '')
	{	
		if ($affiliate_code == "") $affiliate_code = $this->affiliate_code;
		$arr_param = array(
			'merchant_site_code'=>	strval($this->merchant_site_code),
			'return_url'		=>	strval(strtolower($return_url)),
			'receiver'			=>	strval($receiver),
			'transaction_info'	=>	strval($transaction_info),
			'order_code'		=>	strval($order_code),
			'price'				=>	strval($price),
			'currency'			=>	strval($currency),
			'quantity'			=>	strval($quantity),
			'tax'				=>	strval($tax),
			'discount'			=>	strval($discount),
			'fee_cal'			=>	strval($fee_cal),
			'fee_shipping'		=>	strval($fee_shipping),
			'order_description'	=>	strval($order_description),
			'buyer_info'		=>	strval($buyer_info), //"Họ tên người mua *|* Địa chỉ Email *|* Điện thoại *|* Địa chỉ nhận hàng"
			'affiliate_code'	=>	strval($affiliate_code)
		);
		
		$secure_code ='';
		$secure_code = implode(' ', $arr_param) . ' ' . $this->secure_pass;

		$arr_param['secure_code'] = md5($secure_code);		

		$redirect_url = $this->nganluong_url;
		if (strpos($redirect_url, '?') === false) {
			$redirect_url .= '?';
		} else if (substr($redirect_url, strlen($redirect_url)-1, 1) != '?' && strpos($redirect_url, '&') === false) {
			$redirect_url .= '&';			
		}

		$url = '';
		foreach ($arr_param as $key=>$value) {
			$value = urlencode($value);
			if ($url == '') {
				$url .= $key . '=' . $value;
			} else {
				$url .= '&' . $key . '=' . $value;
			}
		}

		return $redirect_url.$url;
	}

	public function verifyPaymentUrl($transaction_info, $order_code, $price, $payment_id, $payment_type, $error_text, $secure_code)
	{
		$str = '';
		$str .= ' ' . strval($transaction_info);
		$str .= ' ' . strval($order_code);
		$str .= ' ' . strval($price);
		$str .= ' ' . strval($payment_id);
		$str .= ' ' . strval($payment_type);
		$str .= ' ' . strval($error_text);
		$str .= ' ' . strval($this->merchant_site_code);
		$str .= ' ' . strval($this->secure_pass);

		$verify_secure_code = '';
		$verify_secure_code = md5($str);

		if ($verify_secure_code === $secure_code) return true;
		else return false;
	}
	
	public function GetTransactionDetails($order_code, $url)
	{	
		$checksum = $order_code."|".$this->secure_pass;
		$params = array(
			'merchant_id' => $this->merchant_site_code ,
			'checksum' => MD5($checksum),
			'order_code'  => $order_code
		);
	
		$api_url = $url;
		$post_field = '';
		foreach ($params as $key => $value){
			if ($post_field != '') $post_field .= '&';
			$post_field .= $key."=".$value;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$api_url);
		curl_setopt($ch, CURLOPT_ENCODING , 'UTF-8');
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
		$result = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		$error = curl_error($ch);
		if ($result != '' && $status==200){					
			return $result;
		}
		
		return false;
	}
}
?>