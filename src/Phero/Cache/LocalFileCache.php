<?php
namespace Phero\Cache;

use Phero\Cache\Enum as enum;
use Phero\Cache\Interfaces as interfaces;

/**
 * 本地文件类关联路由
 * 采用类名和方法名进行关联
 */
class LocalFileCache implements interfaces\ICache {
	CONST commonKey = "common.cache";

	/**
	 * 保存文件缓存
	 * @param  [type] $key       [保存数据的key，或者是数据本身]
	 * @param  [type] $data      [数据]
	 * @param  [type] $life_time [保存的有效时间]
	 * @return [type]            [description]
	 */
	public static function save($key, $data, $life_time = enum\FileTime::FOREVER) {
		if (is_array($key) && empty($data)) {
			$data = $key;
			$filename = LocalFileCache::commonKey;
			$oldData = self::read();
			if ($oldData) {
				$data = array_merge($data, $oldData);
			}
		} else {
			$filename = md5($key);
		}
		$filepath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "CacheFile" . DIRECTORY_SEPARATOR . $filename;
		$file_cursor = fopen($filepath, "w+");
		// if (is_array($data)) {
		$data = ['data' => $data, "time" => $filename, "create_time" => time()];
		$data = serialize($data);
		// }else{

		// }
		$result = fwrite($file_cursor, $data);
		if ($result !== false) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * 读取文件缓存
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public static function read($key) {
		if (empty($key)) {
			$filename = LocalFileCache::commonKey;
		} else {
			$filename = md5($key);
		}
		$filepath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "CacheFile" . DIRECTORY_SEPARATOR . $filename;
		if (!file_exists($filepath)) {
			return false;
		}
		$file_cursor = fopen($filepath, "r");
		$fileLength = filesize($filepath);
		if (empty($fileLength)) {
			return false;
		}
		$result = fread($file_cursor, $fileLength);
		if (($resultUnserze = unserialize($result)) !== false) {
			//判断本地文件是否过期
			if ($resultUnserze['time'] == enum\FileTime::FOREVER) {
				return $resultUnserze['data'];
			} else if (time() - $resultUnserze['create_time'] > $resultUnserze['time']) {
				return false;
			} else {
				return $resultUnserze["data"];
			}
		}
		return false;
	}

	public static function delete($key) {

	}
}