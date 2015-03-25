<?php

/**
 * 
 * @author cartmanchen csz@soyhw.com
 * @date   2014
 * 中国大陆居民身份证验证
 * 可以获得身份证的性别，出生年月日，地区
 * 通过CityCode类的getPlace方法，可以获得省市县的中文表达。
 * 
 * @example
 * $id = new CitizenIdNumber(420323198611103512);
 * $id->isValidate();
 * 
 * or
 * 
 * id = new CitizenIdNumber()
 * $id->isValidate(420323198611103512);
 *
 */

class CitizenIdNumber {
	
	/**
	 * @var string 身份证号码
	 */
	protected $idNumber = null;
	
	/**
	 * 
	 * @var int 身份号码的长度
	 */
	private $idLength = 0;
	
	/**
	 * @var array 加权因子
	 */
	protected $salt = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	
	/**
	 * @var array 校验码
	 */
	protected $checksum = array(1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2);
	
	/**
	 * @var array 身份证号码开始两位的身份编号
	 */
	/*
	protected $citycode = array(
			
		'11','12','13','14','15','21','22',

        '23','31','32','33','34','35','36',

        '37','41','42','43','44','45','46',

        '50','51','52','53','54','61','62',

        '63','64','65','71','81','82','91'
		
	);
	*/
	
	public function __construct($id = null){
		if (!empty($id)){
			$this->setId($id);
		}
	}
	
	public function setId($id){
		if (!$id){
			throw new Exception('param $id must not none');
		}
		$this->idNumber = $id;
		$this->idLength = strlen($id);
	}
	
	/**
	 * 验证号码是否合法
	 * @param string $id
	 * @return boolean
	 */
	public function isValidate($id = null ){
		if (!empty($id)){
			$this->setId($id);
		}
		
		if (empty($this->idNumber)){
			return false;
		}
		
		
		if ($this->checkScheme() 
			&& $this->checkCitycode() 
			&& $this->checkBirthday() 
			&& $this->checkLastCode())
			return true;
		
		return false;
			
		
	}
	
	
	/**
	 * 获取出生年月日,格式 Ymd
	 * @return string
	 */
	public function getBirthday(){
		if ($this->idLength == 18){
			$birthday = substr($this->idNumber, 6, 8);
		} else {
			$birthday = '19' . substr($this->idNumber, 6, 6);
		}
		return $birthday;
	}
	
	/**
	 * 获取性别，1-男，2-女
	 * @return string
	 */
	public function getGender(){
		if ($this->idLength == 18){
			return $this->idNumber{16};
		}
		
		return $this->idNumber{14};
	}
	
	
	
	/**
	 * 检查前6位的地区码是否存在
	 * @return boolean
	 */
	protected function checkCitycode(){
		$city = substr($this->idNumber, 0, 6);
		if (!class_exists('CityCode')){
			include_once dirname(__FILE__) . '/CityCode.php';
		}
		return CityCode::isValidate($city);
	}
	
	/**
	 * 检查号码格式
	 * @return boolean
	 */
	protected function checkScheme(){
		if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $this->idNumber)) return false;
		return true;
	}
	
	/**
	 * 检查出生年月
	 * @return boolean
	 */
	protected function checkBirthday(){
		
		$birthday = $this->getBirthday();
		
		return date('Ymd', strtotime($birthday)) == $birthday;
	}
	
	
	
	
	/**
	 * 校验最后一位校验码
	 * @return boolean
	 */
	protected function checkLastCode(){
		if ($this->idLength == 15){
			return true;
		}
		
		$sum = 0;
		for ($i = 0; $i<17; $i++){
			$sum += $this->idNumber{$i} * $this->salt[$i];
		}
		
		$seek =  $sum % 11;
		
		return $this->checksum[$seek] == strtoupper($this->idNumber{17});
	}
	
	
}
