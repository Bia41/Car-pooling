drop table if exists Utente cascade;
create table if not exists Utente(
	nick varchar(20) not null check(nick <> ''),
	nome varchar(20) not null check(nome <> ''),
	numero int not null,
	saldo numeric(6,2) not null,
	primary key(nick),
	unique(numero));

drop table if exists Viatura cascade;
create table if not exists Viatura(
	matricula char(8) not null check(matricula <> ''),
	marca varchar(20) not null check(marca <> ''),
	modelo varchar(20) not null check(modelo <> ''),
	maxocupantes int not null,
	nick varchar(20),
	primary key(matricula),
	foreign key (nick) references Utente,
	CHECK (maxocupantes >= 2 AND maxocupantes <= 9));

drop table if exists Aluno cascade;
create table if not exists Aluno(
	curso varchar(30) not null check(curso <> ''),
	nick varchar(20),
	primary key(nick),
	foreign key (nick) references Utente);

drop table if exists Docente cascade;
create table if not exists Docente(
	nick varchar(20),
	primary key(nick),
	foreign key(nick) references Utente);

drop table if exists Funcionario cascade;
create table if not exists Funcionario(
	nick varchar(20),
	primary key(nick),
	foreign key(nick) references Utente);

drop table if exists Condutor cascade;
create table if not exists Condutor(
	nick varchar(20),
	primary key(nick),
	foreign key(nick) references Utente);

drop table if exists Passageiro cascade;
create table if not exists Passageiro(
	nick varchar(20),
	primary key(nick),
	foreign key(nick) references Utente);

drop table if exists Local cascade;
create table if not exists Local(
	nome varchar(20) not null check(nome <> ''),
	latitude varchar(15),
	longitude varchar(15),
	primary key (nome));

drop table if exists Trajeto cascade;
create table if not exists Trajeto(
	nome_origem varchar(20),
	nome_destino varchar(20),
	primary key(nome_origem, nome_destino),
	foreign key(nome_origem) references Local,
	foreign key(nome_destino) references Local);

drop table if exists Boleia cascade;
create table if not exists Boleia(
	nick varchar(20),
	nick_condutor varchar(20),
	data_hora timestamp not null,
	custo_passageiro numeric(6,2) not null,
	nome_origem varchar(20),
	nome_destino varchar(20),
	matricula char(8),
	primary key(nick, data_hora),
	foreign key(nick) references Utente,
	foreign key(nick_condutor) references Condutor(nick),
	foreign key(nome_origem, nome_destino) references Trajeto,
	foreign key(matricula) references Viatura,
	CHECK (custo_passageiro > 0));

drop table if exists BoleiaFrequente cascade;
create table if not exists BoleiaFrequente(
	nick varchar(20),
	data_hora timestamp,
	data_termino timestamp not null,
	tipo varchar(30) not null check(tipo <> ''),
	primary key (nick, data_hora),
	foreign key(nick, data_hora) references Boleia);

drop table if exists BoleiaUnica cascade;
create table if not exists BoleiaUnica(
	nick varchar(20) not null check(nick <> ''),
	data_hora timestamp,
	foreign key(nick, data_hora) references Boleia);

drop table if exists Inscricaop cascade;
create table if not exists Inscricaop(
	nick_passageiro varchar(20),
	nick_organizador varchar(20),
	data_hora timestamp,
	primary key(nick_passageiro, nick_organizador, data_hora),
	foreign key(nick_passageiro) references Passageiro(nick),
	foreign key(nick_organizador, data_hora) references Boleia(nick, data_hora));
