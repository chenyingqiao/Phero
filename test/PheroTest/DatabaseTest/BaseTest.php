<?php 

namespace PheroTest\DatabaseTest;

use PHPUnit\Framework\TestCase;
use PheroTest\DatabaseTest\Unit\Children;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\MotherInfo;
use PheroTest\DatabaseTest\Unit\ParentInfo;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\Model;
use Phero\System\DI;
use Webmozart\Console\IO\BufferedIO;
use Webmozart\Console\UI\Component\Table;
/**
 * @Author: lerko
 * @Date:   2017-05-31 15:53:30
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-08-01 22:04:38
 */
class BaseTest extends TestCase
{
	/**
	 * @beforeClass
	 * @Author   Lerko
	 * @DateTime 2017-06-02T09:49:02+0800
	 */
	public static function setUpConfig(){
		self::TablePrint("初始化数据库");
		DI::inj("config",dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
		Parents::Inc()->truncate();
		Mother::Inc()->truncate();
		Marry::Inc()->truncate();
		MotherInfo::Inc()->truncate();
		ParentInfo::Inc()->truncate();
		Children::Inc()->truncate();
		self::_createData();
	}

	/**
	 * @afterClass
	 * @Author   Lerko
	 * @DateTime 2017-06-27T11:42:58+0800
	 * @return   [type]                   [description]
	 */
	public static function tearDownClearData(){
		Parents::Inc()->truncate();
		Mother::Inc()->truncate();
		Marry::Inc()->truncate();
		MotherInfo::Inc()->truncate();
		ParentInfo::Inc()->truncate();
		Children::Inc()->truncate();
		self::TablePrint("清空数据库");
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

	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-02T12:02:59+0800
	 * @return   [type]                   [description]
	 */
	private static function _createData(){
		(new Parents)->truncate();
		(new Mother)->truncate();
		(new Marry)->truncate();
		(new ParentInfo)->truncate();
		(new MotherInfo)->truncate();
		(new Children)->truncate();
		for ($i=0; $i < 10; $i++) {
			$parentsName="parent{$i}";
			$motherName="mother{$i}";
			$UnitsParent[]=new Parents(["name"=>$parentsName]);
			$UnitsMother[]=new Mother(["name"=>$motherName]);
			$UnitsMarry[]=new Marry(["pid"=>$i+1,"mid"=>$i+1]);
			$UnitsParentInfo[]=new ParentInfo(["pid"=>$i+1,"phone"=>"1506013{$i}03"]);
			$UnitsMotherInfo[]=new MotherInfo(["mid"=>$i+1,"email"=>"6143257{$i}@qq.com"]);
			$UnitsChildren[]=new Children(['name'=>"小明{$i}","marry_id"=>$i+1]);
			$UnitsChildren[]=new Children(['name'=>"小黄{$i}","marry_id"=>$i+1]);
		}
		$Model=new Model();
		$Model->insert($UnitsParent);
		$Model->insert($UnitsMother);
		$Model->insert($UnitsMarry);
		$Model->insert($UnitsParentInfo);
		$Model->insert($UnitsMotherInfo);
		$Model->insert($UnitsChildren);

	}
}