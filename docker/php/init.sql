create database if not exists task;
use task;
create table if not exists task (
    id int unsigned not null primary key auto_increment,
    title varchar(32) not null
);
