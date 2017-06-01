<?php 

namespace PheroTest\DatabaseTest;

use PHPUnit\Framework\TestCase;
use Phero\System\DI;
use Webmozart\Console\IO\BufferedIO;
use Webmozart\Console\UI\Component\Table;
/**
 * @Author: lerko
 * @Date:   2017-05-31 15:53:30
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-01 11:47:33
 */
class BaseTest extends TestCase
{
	public function __construct(){
		parent::__construct();
		DI::inj("all_config_path",dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
	}

	/**
	 * @Author   Lerko
	 * @DateTime 2017-05-31T16:13:34+0800
	 * @return   [type]                   [description]
	 */
	public function prient_format(){
		$data=[
			"asdf",
			"2323",
			"234dsfa"
		];
		$this->TablePrint($data);
		$data="asdfhasdf";
		$this->TablePrint($data);
	}

	/**
	 * 格式化输出数据
	 * @Author   Lerko
	 * @DateTime 2017-05-31T15:59:36+0800
	 * @param    [type]                   $data [description]
	 * @return   [type]                         [description]
	 */
	protected function TablePrint($data){
		$io=new BufferedIO();
		$table = new Table();
		is_array($data)?"":$data=[$data];
		foreach ($data as $value) {
			if(!is_array($value))
		    	$value=[$value];
		    $table->addRow($value);
		}
		$table->render($io);
		echo "\n".$io->fetchOutput();
	}
}