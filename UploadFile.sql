create database file;
create table insertfile(
id int primary key auto_increment,
filepath varchar(255)
);

select * from insertfile;

create table courses (
id int primary key auto_increment,
category varchar(50),
title varchar(100),
duration varchar(10)
);

insert into courses(id,category,title,duration) 
values(3,'Design','Learn Figma: This is some dummy text demonistrating the title', '5-8 hrs' ),
(4,'Entertainment','Learn Film Making: This is some dummy text demonistrating the title', '2-10 hrs' );

select * from courses;

create table courses_info(
id int primary key auto_increment,
title varchar(100),
main_description varchar(1000),
learning_outcomes varchar(500),
course_content varchar(500)
);
select * from courses_info;

 insert into courses_info(id,title,main_description,learning_outcomes,course_content)
 values(1,'Introduction to Php','Learn how to dynamically generate html lists using php and data fetched from sql.', 'Understand sql data structure; Use php explode() function; Generate dynamic 
 HTML lists; Apply custom css to generate lists','Data Fetching; String Manupulation; Outputting HTML');
 
 update courses_info set main_description='Lorem ipsum odor amet, consectetuer adipiscing elit. Ut litora dictum nisi tristique purus fames porttitor. Habitasse fusce dui ac accumsan ut suscipit tempus
 tristique placerat. Convallis accumsan adipiscing duis tincidunt ac curabitur. Per nulla gravida parturient nulla nascetur commodo litora accumsan convallis turpis. Porta curae augue nec congue diam 
 eros ipsum orci erat ut ex. Magna dictum sollicitudin eu eget non, sociosqu lectus suscipit nec. Aliquam dis ante turpis interdum vulputate taciti fringilla dui tempus aliquam. Nisi curae consequat
 taciti ridiculus blandit velit magna imperdiet proin habitant. Penatibus at ad dapibus turpis nisl egestas tempor mollis tristique. Rutrum pharetra auctor porttitor montes a fringilla tempus felis 
 suscipit elit suspendisse lectus.' where id=1 ;
 
 update courses_info set course_content = 'chapter 1: Data Fetching;chapter 2: String Manupulation;chapter 3: Outputting HTML' where id=1;
 drop table course;