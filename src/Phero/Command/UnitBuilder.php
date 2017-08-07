<?php

namespace Phero\Command;

use League\CLImate\CLImate;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\Map\Note\Field;
use Phero\Map\Note\RelationEnable;
use Phero\Map\Note\Table;
use Phero\System\DI;
use Webmozart\Console\Config\DefaultApplicationConfig;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\PropertyGenerator;
/**
 * @Author: lerko
 * @Date:   2017-06-19 20:02:38
 * @Last Modified by:   Administrator
 * @Last Modified time: 2017-08-07 17:03:50
 */
class UnitBuilder
{
	public function Builder()
	{
		$climate=new CLImate;
		$climate->bold()->backgroundBlue()->border();

		$input = $climate->input('请输入生成文件的位置：');
		$fileFloder = $input->prompt();
		if(!is_dir($fileFloder)){
			mkdir(iconv("UTF-8", "GBK", $fileFloder),0777,true);
		}

		$input=$climate->input("输入统一的命名空间：");
		$namespace=$input->prompt();
		$namespace=$this->replaceSp($namespace);

		$input=$climate->input("输入数据库名称：");
		$dbname=$input->prompt();


		$input=$climate->input("输入数据库地址：(默认 127.0.0.1)");
		$input->defaultTo('127.0.0.1');
		$host=$input->prompt();

		$input=$climate->input("输入数据库用户名：(默认 root)");
		$input->defaultTo('root');
		$username=$input->prompt();

		$input=$climate->input("输入数据库密码：(默认为空)");
		$input->defaultTo('');
		$password=$input->prompt();

		$input=$climate->input("是否只生成某些表？(默认为全部，表名逗号隔开)");
		$input->defaultTo('');
		$tables_input=$input->prompt();
		if(!empty($tables_input)){
			$tables_input=explode(",",$tables_input);
		}

		DI::inj("pdo_instance",new \PDO("mysql:dbname={$dbname};host={$host}",$username,$password));
		$DbHelp=new MysqlDbHelp();
		$tables=$DbHelp->queryResultArray("show tables");
		$tables_gen=[];
		if(!empty($tables_input)){
			foreach ($tables_input as $key => $value) {
				if(in_array($value,$tables_input)){
					$tables_gen[]=$value;
				}else{
					$climate->red("$value 不存在");
				}
			}
		}else{
			$tables_gen=$tables;
		}
		$progress=$climate->progress()->total(count($tables_gen));
		foreach ($tables_gen as $key => $value) {
			$value=$value["Tables_in_{$dbname}"];
			$progress->current($key + 1);
			$this->_createPhp($value,$dbname,$namespace,$fileFloder);
		}
	}

	/**
	 * 创建php文件
	 * @Author   Lerko
	 * @DateTime 2017-06-27T10:36:25+0800
	 * @param    [type]                   $tablename [description]
	 * @param    [type]                   $dbname    [description]
	 * @return   [type]                              [description]
	 */
	private function _createPhp($tablename,$dbname,$namespace,$fileFloder){
		$classname=$this->splitTableName($tablename);
		$classes=$this->_createDbUnit($classname,$namespace);
		$tableNode=new Table();
		$tableNode->name=$tablename;
		$tableNode->alias=$this->base64_sp($tablename);
		$classes->setDocblock($this->_createTableDocBlock($tableNode));
		$DbHelp=new MysqlDbHelp();
		$field=$DbHelp->queryResultArray("select * from information_schema.columns where table_schema = '{$dbname}' and table_name = '{$tablename}';");
		foreach ($field as $key => $value) {
			if(strstr($value["DATA_TYPE"],"int")!==false)
				$type="int";
			else
				$type="string";
			if(strstr($value['COLUMN_KEY'],'PRI')!==false)
				$is_primary=true;
			else
				$is_primary=false;
			$classes->addPropertyFromGenerator($this->_createProperty($value['COLUMN_NAME'],$type,$tablename,$value['COLUMN_COMMENT'],$is_primary));
		}
		$content=$classes->generate();
		file_put_contents($fileFloder."/".$classname.".php", "<?php\n".$content);
	}

	//创建类
	private function _createDbUnit($name,$namespace){
		$classgenerator=new ClassGenerator;
		$classgenerator->setExtendedClass("Phero\Database\DbUnit")
				->setName($name)
				 ->setNamespaceName($namespace);
		return $classgenerator;
	}

	/**
	 * 创建Table的注解
	 * @Author   Lerko
	 * @DateTime 2017-06-26T11:19:35+0800
	 * @param    Table                    $tableNode [description]
	 * @param    RelationEnable|null      $relation  [description]
	 * @return   [type]                              [description]
	 */
	private function _createTableDocBlock(Table $tableNode,RelationEnable $relation=null){
		$GeneratorData=[];
		$description="[table={$tableNode->name},";
		$alias=$tableNode->alias;
		if(empty($alias)){$description.="]";}else{$description.="alias=$alias]";}
		$GeneratorData['tags'][]=["name"=>"Table","description"=>$description];
		if($relation){
			$GeneratorData['tags'][]=['name'=>"RelationEnable"];
		}
		return DocBlockGenerator::fromArray($GeneratorData);
	}

	/**
	 * 创建property的注解
	 * @Author   Lerko
	 * @DateTime 2017-06-26T11:19:58+0800
	 * @param    Field                    $field [description]
	 * @return   [type]                          [description]
	 */
	private function _createPropertyDocBlock(Field $field,$discription="",$is_primary=false){
		$GeneratorData=[];
		$name=$field->name;
		$type=$field->type;
		$alias=$field->alias;
		if(isset($name))
			$description="[name={$name}";

		if(isset($type))$description.=",type={$type}";
		if(isset($alias))$description.=",alias={$alias}]";
		else $description.="]";
		$GeneratorData['tags'][]=["name"=>"Field","description"=>$description];
		$GeneratorData['longDescription']=$discription;
		if($is_primary)
			$GeneratorData['tags'][]=["name"=>"Primary","description"=>""];
		return DocBlockGenerator::fromArray($GeneratorData);
	}

	/**
	 * 创建新的property
	 * @Author   Lerko
	 * @DateTime 2017-06-26T11:21:44+0800
	 * @param    [type]                   $name [description]
	 * @return   [type]                         [description]
	 */
	private function _createProperty($name,$type,$tablename,$discription="",$is_primary=false){
		$field=new Field();
		$field->name=$name;
		$field->type=$type;
		$field->alias=$this->base64_sp($tablename.".".$name);
		return (new PropertyGenerator($name))->setDocBlock($this->_createPropertyDocBlock($field,$discription,$is_primary));
	}

	/**
	 * 将驼峰法的表明变成大小写形式
	 * @Author   Lerko
	 * @DateTime 2017-06-26T18:04:40+0800
	 * @param    [type]                   $tablename [description]
	 * @return   [type]                              [description]
	 */
	private function splitTableName($tablename,$prefix=false){
		$name_arr=explode('_', $tablename);
		if($prefix) array_shift($name_arr);
		$name="";
		foreach ($name_arr as $key => $value) {
			$name.=ucfirst($value);
		}
		return $name;
	}

	/**
	 * 替换命名空间的/
	 * @Author   Lerko
	 * @DateTime 2017-06-27T09:34:53+0800
	 * @param    [type]                   $string [description]
	 * @return   [type]                           [description]
	 */
	private function replaceSp($string){
		return str_replace("/", '\\', $string);
	}

	private function base64_sp($string,$encode=true){
		if($encode) return str_replace("=","_",base64_encode($string));
		else return str_replace("_","=",base64_decode($string));
	}
}
