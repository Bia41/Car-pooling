--Query A
select B.nick, B.nick_condutor, B.data_hora, B.custo_passageiro, B.nome_origem, B.nome_destino, I.nick_passageiro, B.matricula, null as marca, null as modelo, null as maxocupantes 
from Boleia B, inscricaoP I 
where B.nick = I.nick_organizador and B.data_hora = I.data_hora and B.nick_condutor IS NULL

union 

select B.nick, B.nick_condutor, B.data_hora, B.custo_passageiro, B.nome_origem, B.nome_destino, I.nick_passageiro, V.matricula, V.marca, V.modelo, V.maxocupantes 
from Boleia B, inscricaoP I, viatura V 
where B.nick = I.nick_organizador and B.data_hora = I.data_hora and B.matricula = V.matricula
order by data_hora, nick ASC;

--Query B

select nome, maximo
from (select avg(total) as media, nick_condutor
	from (select nick_condutor, custo_passageiro, numpassageiros, custo_passageiro * numpassageiros as total 
		from boleiaunica natural join boleia natural join
			(select count(nick_passageiro) as numPassageiros, nick_organizador as nick, data_hora
			from inscricaoP
			group by nick_organizador, data_hora) as Passageiros
		where nick_condutor is not null and (nome_origem = 'IST-Tagus' or nome_destino = 'IST-Tagus')) as Totais
	group by nick_condutor) as Medias,
	(select max(media) as maximo
	from (select avg(total) as media, nick_condutor
		from (select nick_condutor, custo_passageiro, numpassageiros, custo_passageiro * numpassageiros as total 
			from boleiaunica natural join boleia natural join
				(select count(nick_passageiro) as numPassageiros, nick_organizador as nick, data_hora
				from inscricaoP
				group by nick_organizador, data_hora) as Passageiros
			where nick_condutor is not null and (nome_origem = 'IST-Tagus' or nome_destino = 'IST-Tagus')) as Totais
		group by nick_condutor) as Medias) as MaxMedia,
	Utente
where media = maximo and nick_condutor = nick;

--Query C

--Para uma origem especifica, neste caso 'Lisboa'
select *
from (select count(nome_destino) as nDestinos
	from Trajeto
	where nome_origem = 'Lisboa') as D,

	(select count(nome_destino) as nDestinosNick, nick 
	from (select distinct nome_destino, nick
		from (select nome_destino, nick_passageiro as nick
			from Boleia B natural join InscricaoP I 
			where nome_origem = 'Lisboa' and B.data_hora = I.data_hora and B.nick = I.nick_organizador
			UNION
			select nome_destino, nick_condutor as nick
			from boleia
			where nome_origem = 'Lisboa' and nick_condutor is not null) as destNick) as distinctDestNick 
	group by nick) as distinctNickCount
where nDestinos = nDestinosNick;

--Para todas as origens possíveis
select nome_origem, nick, ndestinosorigem as ndestinos
from (select count(nome_destino) as nDestinosOrigem, nome_origem
	from Trajeto
	group by nome_origem) as Destinos natural join

	(select count(nome_destino) as nDestinosNick, nick, nome_origem
		from (select distinct nome_destino, nick, nome_origem
			from (select nome_destino, nick_passageiro as nick, nome_origem
				from Boleia B natural join InscricaoP I 
				where B.data_hora = I.data_hora and B.nick = I.nick_organizador
				UNION
				select nome_destino, nick_condutor as nick, nome_origem
				from boleia
				where nick_condutor is not null) as destNick) as distinctDestNick 
		group by nome_origem, nick) as DestNickOrigem
where ndestinosorigem = ndestinosnick;
