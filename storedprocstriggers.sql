create or replace function Inscricaop_trigger_proc() returns trigger
as $$
declare
	saldo_inicial numeric(6,2);
	custo_boleia numeric(6,2);
begin
	select saldo into saldo_inicial
	from Utente
	where nick = new.nick_passageiro;

	select custo_passageiro into custo_boleia
	from Boleia
	where nick = new.nick_organizador and data_hora = new.data_hora;

	if saldo_inicial < custo_boleia then
		raise exception 'Saldo insuficiente';
	end if;

	update Utente set saldo = saldo_inicial - custo_boleia
	where nick = new.nick_passageiro;

	return new;
end

$$ language plpgsql;

drop trigger if exists Inscricaop_trigger on Inscricaop cascade;
create trigger Inscricaop_trigger before insert on Inscricaop
	for each row execute procedure Inscricaop_trigger_proc();
	
create or replace function Inscricaoc_trigger_proc() returns trigger
as $$
declare
	numero_passageiros int;
	max_ocupantes int;
	matr char(8);
	n_linhas int;

begin
	if new.nick_condutor is NULL then
		return new;
	end if;
	
	select count(nick_passageiro) into numero_passageiros
	from Inscricaop
	where nick_organizador = new.nick and data_hora = new.data_hora;

	select maxocupantes into max_ocupantes 
	from Viatura
	where matricula = new.matricula;
fins
	if max_ocupantes <= numero_passageiros then
		raise exception 'Lugares insuficientes';
	end if;

	return new;
end

$$ language plpgsql;

drop trigger if exists Inscricaoc_trigger on Boleia cascade;
create trigger Inscricaoc_trigger before insert or update on Boleia
	for each row execute procedure Inscricaoc_trigger_proc();
