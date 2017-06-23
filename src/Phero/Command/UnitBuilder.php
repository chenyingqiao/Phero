<?php 

use Webmozart\Console\Config\DefaultApplicationConfig;
use Zend\Code\Generator\ClassGenerator;
/**
 * @Author: lerko
 * @Date:   2017-06-19 20:02:38
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-23 18:19:59
 */
class UnitBuilder extends DefaultApplicationConfig
{
	protected function configure()
	{
		
	}

	private function createDbUnit(){
		$classgenerator=new ClassGenerator;
		$classgenerator->setExtendedClass("PheroTest\DatabaseTest\Unit\DbUnit");
	}

	private function createDocBlock($name,$alias="",$relation=true,$description=""){
		$GeneratorData=[];
		$GeneratorData['longDescription']=$description;
		$name="[table=$name,";
		if(empty($alias)){$name.="]";}else{$name.=",alias=$alias]";}
		$GeneratorData['tags'][]=["name"=>"Table","description"=>"[table=$name,alias=$alias]"];
		if($relation){
			$GeneratorData['tags']=['name'=>"RelationEnable"];
		}
		return DocBlockGenerator::fromArray($GeneratorData);
	}

	private function createProperty($name,$alias=null,$type="string"){

	}
}