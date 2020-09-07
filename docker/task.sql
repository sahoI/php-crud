create database if not exists taskDB;
use taskDB;
create table if not exists task (
    id int unsigned not null primary key auto_increment,
    title varchar(30) not null,
    description varchar(100) not null
)
