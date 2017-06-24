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
 * @Last Modified time: 2017-06-08 14:36:48
 */
class BaseTest extends TestCase
{
	/**
	 * @beforeClass
	 * @Author   Lerko
	 * @DateTime 2017-06-02T09:49:02+0800
	 */
	public static function setUpConfig(){
		self::TablePrint("初始化配置文件位置");
		DI::inj("config",dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
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

	protected $time_start,$time_end;
	/**
	 * 记录运行时间
	 * @Author   Lerko
	 * @DateTime 2017-06-02T10:18:59+0800
	 * @param    boolean                  $type [true 开始 false 结束]
	 * @return   [type]                         [description]
	 */
	protected function timer($type=true,$msg="没有消息"){
		if($type){
			$this->time_start=microtime(true);
		}else{
			$this->time_end=microtime(true);
			if(isset($this->time_start)){
				$time="耗时".round($this->time_end-$this->time_start,5)."秒";
				$this->TablePrint([$msg,$time]);
			}
		}
	}
}