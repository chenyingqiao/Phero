<?php
namespace Phero\Cache\Interfaces;

interface ICache {
	public static function save($key, $data,$lt=null);
	public static function read($key);
	public static function delete($key);
}