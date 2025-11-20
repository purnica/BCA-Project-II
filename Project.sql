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