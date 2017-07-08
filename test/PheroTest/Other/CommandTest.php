<?php 

namespace PheroTest\Other;

use League\CLImate\CLImate;
use PheroTest\DatabaseTest\BaseTest;
/**
 * @Author: lerko
 * @Date:   2017-06-23 16:46:35
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-27 11:16:49
 */

class CommandTest extends BaseTest
{
	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-23T16:47:46+0800
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function simpleOutPut($value='')
	{
		$climate=new CLImate;
		$climate->out("this is climate hello world");
		$climate->to('error')->red('Something went terribly wrong.');
		$climate->red('Whoa now this text is red.');
		$climate->bold()->blue('Blue? Wow!');
		$climate->lightGreen('It is not easy being (light) green.');
		$climate->backgroundRed('Whoa now this text has a red background.');
		$climate->backgroundBlue()->out('Blue background? Wow!');
		$climate->backgroundLightGreen()->out('It is not easy being (light) green (background).');

		$climate->error('Ruh roh.');
		$climate->comment('Just so you know.');
		$climate->whisper('Not so important, just a heads up.');
		$climate->shout('This. This is important.');
		$climate->info('Nothing fancy here. Just some info.');

		//输出表格
		$climate->redTable([
		    [
		      'name'       => 'Walter White',
		      'role'       => 'Father',
		      'profession' => 'Teacher',
		    ],
		    [
		      'name'       => 'Skyler White',
		      'role'       => 'Mother',
		      'profession' => 'Accountant',
		    ],
		]);
		//输出分割线
		$climate->bold()->backgroundBlue()->border();

		$climate->underlineJson([
		  'name' => 'Gary',
		  'age'  => 52,
		  'job'  => '<blink>Engineer</blink>',
		]);

		// //输入
		// $input = $climate->input('How you doin?');
		// $response = $input->prompt();

		// //输入选项
		// $input = $climate->input('How you doin?');
		// $input->accept(['Fine', 'Ok']);
		// $response = $input->prompt();

		//选项进度条
		$languages = [
		    'php',
		    'javascript',
		    'python',
		    'ruby',
		    'java',
		];

		$progress = $climate->progress()->total(count($languages));

		foreach ($languages as $key => $language) {
		  $progress->current($key + 1);

		  // Simulate something happening
		  usleep(80000);
		}
	}
}