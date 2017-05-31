<?php 

namespace PheroTest\DatabaseTest;

use PHPUnit\Framework\TestCase;
/**
 * @Author: lerko
 * @Date:   2017-05-31 15:53:30
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-05-31 15:59:42
 */
class BaseTestCase extends TestCase
{
	public function __construct(){
		parent::__construct();
		DI::inj("all_config_path",dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
	}

	/**
	 * 格式化输出数据
	 * @Author   Lerko
	 * @DateTime 2017-05-31T15:59:36+0800
	 * @param    [type]                   $data [description]
	 * @return   [type]                         [description]
	 */
	protected function print_format($data){
		$print="-------------\n";
		foreach ($data as $key => $value) {
			$row="";
			if(is_array($value)){
				foreach ($value as $key2 => $value2) {
					$row.=$value2."   ";
				}
			}else{
				$row=$value;
			}
			$print=$row."\n";
		}
		$print.="-------------\n";
		return $print;
	}
}