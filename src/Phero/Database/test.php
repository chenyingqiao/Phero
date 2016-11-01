<?php 
try {
	$pdo=new \PDO("mysql:host=localhost;dbname=video;charset=utf8",'root','Cyq19931115');
	$sql="select * from video_cat where id=:id";
	$PdoStatement= $pdo->prepare($sql);
	$id=32;//这里必须用引用的方式
	// $PdoStatement->bindParam(":id", $id,\PDO::PARAM_INT);//绑定一个数据
	//通过fetch获取
	$data=[
		":id"=>32
	];
	$PdoStatement->execute($data);
	$rowCount=$PdoStatement->rowCount();
	echo "count:".$rowCount;
	//
	$return=$PdoStatement->fetch(\PDO::FETCH_OBJ);
	$PdoStatement->closeCursor();

	echo "<pre>";
	var_dump($return);
	echo "</pre>";
} catch (Exception $e) {
	echo $e;
}


/**
 * 如何通过数据excute一条有数据的命令
 */

// $data = array(
//   array('name' => 'John', 'age' => '25'),
//   array('name' => 'Wendy', 'age' => '32')
// );

// try {
//   $pdo = new PDO('sqlite:myfile.sqlite');
// }

// catch(PDOException $e) {
//   die('Unable to open database connection');
// }

// $insertStatement = $pdo->prepare('insert into mytable (name, age) values (:name, :age)');

// // start transaction
// $pdo->beginTransaction();

// foreach($data as &$row) {
//   $pdo->execute($row);
// }

// // end transaction
// $pdo->commit();


/**
 * 如何吧数据fatch到一个实体类中
 */

// <?php
// include_once("user.class");
// $sth = $db->prepare("SELECT * FROM user WHERE id = 1");

// /* create instance automatically */
// $sth->setFetchMode( PDO::FETCH_CLASS, 'user');
// $sth->execute();
// $user = $sth->fetch( PDO::FETCH_CLASS );
// $sth->closeCursor();
// print ($user->id);

// /* or create an instance yourself and use it */
// $user= new user();
// $sth->setFetchMode( PDO::FETCH_INTO, $user);
// $sth->execute();
// $user= $sth->fetch( PDO::FETCH_INTO );
// $sth->closeCursor();
// print ($user->id);
// ?>