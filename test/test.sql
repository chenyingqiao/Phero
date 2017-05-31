/*
* @Author: lerko
* @Date:   2017-05-31 12:12:49
* @Last Modified by:   lerko
* @Last Modified time: 2017-05-31 15:52:32
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
