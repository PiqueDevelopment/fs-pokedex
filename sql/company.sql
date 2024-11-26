create table DEPARTMENT (
  Dname varchar(20) not null,
  Dnumber int(2) not null check (Dnumber > 0 and Dnumber < 21),  
  Mgr_ssn char(9) not null,   
  Mgr_start_date date not null, 
  primary key (Dnumber), 
  unique (Dname)
)ENGINE = INNODB;

create table EMPLOYEE (
  Fname varchar(15) not null,
  Lname varchar(15) not null,
  Ssn char(9) not null,  
  Bdate date not null,
  Address varchar(30) not null,
  Sex char(1) not null,
  Salary decimal(10,2) not null,
  Super_ssn char(9),
  Dno int(2) not null,
  constraint Fullname UNIQUE(Fname, Lname),  
  primary key (Ssn), 
  foreign key (Dno) references DEPARTMENT(Dnumber)
)ENGINE = INNODB;

create table PROJECT (
  Pname varchar(20) not null,
  Pnumber int(4) not null,  
  Plocation varchar(20) not null,
  Dnum int(2) not null, 
  primary key (Pnumber), 
  unique (Pname), 
  foreign key (Dnum) references DEPARTMENT(Dnumber)
)ENGINE = INNODB; 

create table WORKS_ON (
  Essn char(9) not null,  
  Pno int(2) not null, 
  Hours decimal(3,1),  
  primary key (Essn, Pno), 
  foreign key (Essn) references EMPLOYEE(Ssn),
  foreign key (Pno) references PROJECT(Pnumber)
)ENGINE = INNODB; 

create table DEPENDENT (
  Essn char(9) not null,  
  Dependent_name varchar(15) not null,
  Sex char(1) not null,
  Bdate date not null,
  Relationship varchar(8) not null,
  primary key (Essn, Dependent_name),
  foreign key (Essn) references EMPLOYEE(Ssn)
)ENGINE = INNODB; 

create table DEPT_LOCATIONS (
  Dnumber int(2) not null,
  Dlocation varchar(15) not null,
  primary key (Dnumber, Dlocation), 
  foreign key (Dnumber) references DEPARTMENT(Dnumber)
)ENGINE = INNODB;

insert into DEPARTMENT values 
 ('Research',5,333445555,'2020-05-22'),
 ('Administration',4,987654321,'2012-01-01'),
 ('Headquarters',1,888665555,'2018-06-19');

insert into EMPLOYEE values 
 ('John','Smith',123456789,'1975-01-09','731 Fondren, Houston TX','M',80000,333445555,5),
 ('Franklin','Wong',333445555,'1985-12-08','638 Voss, Houston TX','M',90000,888665555,5),
 ('Alicia','Zelaya',999887777,'2000-01-19','3321 Castle, Spring TX','F',95000,987654321,4),
 ('Jennifer','Wallace',987654321,'1990-06-20','291 Berry, Bellaire TX','F',83000,888665555,4),
 ('Ramesh','Narayan',666884444,'1982-09-15','975 Fire Oak, Humble TX','M',78000,333445555,5),
 ('Joyce','English',453453453,'1992-07-31','5631 Rice, Houston TX','F',75000,333445555,5),
 ('Ahmad','Jabbar',987987987,'1989-03-29','980 Dallas, Houston TX','M',85000,987654321,4),
 ('James','Borg',888665555,'1967-11-10','450 Stone, Houston TX','M',105000,null,1);

insert into PROJECT values 
 ('ProductX',1,'Bellaire',5),
 ('ProductY',2,'Sugarland',5),
 ('ProductZ',3,'Houston',5),
 ('Computerization',10,'Stafford',4),
 ('Reorganization',20,'Houston',1),
 ('Newbenefits',30,'Stafford',4);

insert into WORKS_ON values 
 (123456789,1,32.5),
 (123456789,2,7.5),
 (666884444,3,40.0),
 (453453453,1,20.0),
 (453453453,2,20.0),
 (333445555,2,10.0),
 (333445555,3,10.0),
 (333445555,10,10.0),
 (333445555,20,10.0),
 (999887777,30,30.0),
 (999887777,10,10.0),
 (987987987,10,35.0),
 (987987987,30,5.0),
 (987654321,30,20.0),
 (987654321,20,15.0),
 (888665555,20,null);

insert into DEPENDENT values 
 (333445555,'Alice','F','2013-04-04','Daughter'),
 (333445555,'Theodore','M','2010-10-25','Son'),
 (333445555,'Joy','F','1982-05-03','Spouse'),
 (987654321,'Abner','M','1989-02-28','Spouse'),
 (123456789,'Michael','M','2001-01-04','Son'),
 (123456789,'Alice','F','2005-12-30','Daughter'),
 (123456789,'Elizabeth','F','1978-05-05','Spouse');

insert into DEPT_LOCATIONS values
 (1,'Houston'),
 (4,'Stafford'),
 (5,'Bellaire'),
 (5,'Sugarland'),
 (5,'Houston');

alter table DEPARTMENT 
 add constraint depemp foreign key (Mgr_ssn) references EMPLOYEE(Ssn);

alter table EMPLOYEE   
 add constraint empemp foreign key (Super_ssn) references EMPLOYEE(Ssn);
	