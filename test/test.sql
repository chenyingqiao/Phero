/*
* @Author: lerko
* @Date:   2017-05-31 12:12:49
* @Last Modified by:   lerko
* @Last Modified time: 2017-06-02 13:48:06
*/
show tables;


show create table Children;
select * from Children limit 30;

show create table Marry;
select * from Marry limit 30;

show create table Mother;
select * from Mother limit 30;

show create table MotherInfo;
select * from MotherInfo limit 30;

show create table Parent;
select * from Parent limit 30;

show create table ParentInfo;
select * from ParentInfo limit 30;

truncate table Children
truncate table Marry
truncate table Mother
truncate table MotherInfo
truncate table Parent
truncate table ParentInfo


explain select count(*) as count from `Mother` limit 1;

select `parent`.`id`,`parent`.`name` from `Parent` as `parent` where `parent`.`id` = 10;

select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`Mother`.`id`,`Mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.pid=`parent`.id  inner join `Mother` on `Marry`.mid=`Mother`.id ;

select `parent`.`id`,`parent`.`name` from `Parent` as `parent` where (Fun(`parent`.`id`) = 10  and Fun2(`parent`.`name`) like '%test%');