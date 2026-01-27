create database project6;

create table course (
id int primary key auto_increment,
category varchar(50),
duration varchar(10),
title varchar(100),
primary_description varchar(1000),
learning_outcomes varchar(500),
image varchar(255)
);

select * from course;

-- learners login
create table learnerlogin(
learner_id int primary key auto_increment,
firstname varchar(30),
lastname varchar(30),
email varchar(30),
password varchar(100)
);
select * from learnerlogin;

create table learnerinterests(
learnerinterest_id int primary key auto_increment,
learner_id int,
interests varchar(20),
foreign key(learner_id) references learnerlogin(learner_id)
);

-- for admin login
create table admin(
id int primary key auto_increment,
email varchar(30),
password varchar(100)
);
insert into admin (email, password) values("purnika@gmail.com","purnika"),("upasana@gmail.com", "upasana");
insert into admin (email, password) values("admin@gmail.com","admin123");
alter table admin
add name varchar(10);
update admin set name= 'Purnika' where id=1;
update admin set name= 'Upasana' where id=2;
update admin set name= 'Admin' where id=3;
select * from admin;

