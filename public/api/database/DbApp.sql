create table usuarios
(
	id int auto_increment
		primary key,
	usuario varchar(128) null,
	nome varchar(128) null,
	senha varchar(32) null,
	desativado datetime null comment 'nulo = conta ativa',
	criado_em datetime default current_timestamp() not null,
	atualizado_em datetime default current_timestamp() null on update current_timestamp(),
	constraint usuarios_usuario_uindex
		unique (usuario)
);

create table clientes
(
	id int auto_increment
		primary key,
	nome varchar(128) null,
	cpf varchar(11) null,
	criado_em datetime default current_timestamp() null,
	criado_por int null,
	constraint clientes_usuarios_id_fk
		foreign key (criado_por) references usuarios (id)
			on update cascade on delete set null
);

create table produtos
(
	id int auto_increment
		primary key,
	nome varchar(64) null,
	preco decimal(6,2) default 0.00 not null,
	codigo varchar(64) null,
	original int null comment 'este registro eh uma modificacao de outro, que foi descontinuado',
	criado_em datetime default current_timestamp() null,
	criado_por int null,
	deletado_em datetime null,
	deletado_por int null,
	constraint produto_usuarios_id_fk
		foreign key (criado_por) references usuarios (id)
			on update cascade on delete set null,
	constraint produto_usuarios_id_fk_2
		foreign key (deletado_por) references usuarios (id)
			on update cascade on delete set null,
	constraint produtos_produtos_id_fk
		foreign key (original) references produtos (id)
			on update cascade on delete set null
);

create table sessoes
(
	id int auto_increment
		primary key,
	usuario int not null,
	chave varchar(32) not null,
	criado_em datetime default current_timestamp() null,
	constraint sessoes_usuarios_id_fk
		foreign key (usuario) references usuarios (id)
			on update cascade on delete cascade
);

create table vendas
(
	id int auto_increment
		primary key,
	credito decimal(8,2) null,
	cliente int null comment 'nulo = anonimo',
	criado_em datetime default current_timestamp() null,
	criado_por int null,
	atualizado_em datetime default current_timestamp() null on update current_timestamp(),
	constraint vendas_clientes_id_fk
		foreign key (cliente) references clientes (id)
			on update cascade on delete set null,
	constraint vendas_usuarios_id_fk
		foreign key (criado_por) references usuarios (id)
			on update cascade on delete set null
);

create table vendas_itens
(
	id int auto_increment
		primary key,
	venda int not null,
	produto int not null,
	quantidade float null,
	preco_unitario decimal(6,2) null,
	valor decimal(6,2) null,
	criado_em datetime default current_timestamp() not null,
	criado_por int null,
	constraint vendas_itens_produtos_id_fk
		foreign key (produto) references produtos (id)
			on update cascade,
	constraint vendas_itens_usuarios_id_fk
		foreign key (criado_por) references usuarios (id)
			on update cascade on delete set null,
	constraint vendas_itens_vendas_id_fk
		foreign key (venda) references vendas (id)
			on update cascade on delete cascade
);


