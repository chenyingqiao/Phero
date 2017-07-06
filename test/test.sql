/*
* @Author: lerko
* @Date:   2017-05-31 12:12:49
* @Last Modified by:   lerko
* @Last Modified time: 2017-06-14 15:34:39
*/ 
SHOW TABLES;

SHOW
CREATE TABLE Children;


SELECT *
FROM Children
LIMIT 30;

SHOW
CREATE TABLE Marry;


SELECT *
FROM Marry
LIMIT 30;

SHOW
CREATE TABLE Mother;


SELECT *
FROM Mother
LIMIT 30;

SHOW
CREATE TABLE MotherInfo;


SELECT *
FROM MotherInfo
LIMIT 30;

SHOW
CREATE TABLE Parent;


SELECT *
FROM Parent
LIMIT 30;

SHOW
CREATE TABLE ParentInfo;


SELECT *
FROM ParentInfo
LIMIT 30;


SELECT count(*) AS COUNT
FROM `Mother`
LIMIT 1;


truncate table Children;
truncate table Marry;
truncate table Mother;
truncate table MotherInfo;
truncate table Parent;
truncate table ParentInfo;

SELECT `parent`.`id`,
       `parent`.`name`
FROM `Parent` AS `parent`
WHERE `parent`.`id` = 10;


SELECT `Marry`.`id`,
       `Marry`.`pid`,
       `Marry`.`mid`,
       `parent`.`id`,
       `parent`.`name`,
       `Mother`.`id`,
       `Mother`.`name`
FROM `Marry`
INNER JOIN `Parent` AS `parent` ON `Marry`.pid=`parent`.id
INNER JOIN `Mother` ON `Marry`.mid=`Mother`.id ;


SELECT `parent`.`id`,
       `parent`.`name`
FROM `Parent` AS `parent`
WHERE (Fun(`parent`.`id`) = 10
       AND Fun2(`parent`.`name`) LIKE '%test%');


SELECT `Mother`.`mid`,
       `Mother`.`name`
FROM `Mother`
LIMIT 10;


SELECT `Marry`.`id`,
       `Marry`.`pid`,
       `Marry`.`mid`,
       `parent`.`id`,
       `parent`.`name`,
       `Mother`.`id`,
       `Mother`.`name`
FROM `Marry`
INNER JOIN `Parent` AS `parent` ON `Marry`.`pid`=`parent`.`id`
INNER JOIN `Mother` ON `Marry`.`mid`=`Mother`.`id`
GROUP BY `Mother`.`id`
HAVING `Mother`.`id` = 1;

select count(*) from Parent;

update `Mother` as mother set `id`=12,`name`='update Relation 测试';