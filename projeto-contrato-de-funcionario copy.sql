USE project_servico;


CREATE TABLE pessoa (
    IDpessoa_PK INT PRIMARY KEY AUTO_INCREMENT,
    senha_pessoa VARCHAR(100),
    CPF VARCHAR(11) UNIQUE,
    nome VARCHAR(20),

    
);

CREATE TABLE endereco (
    IDendereco_PK INT PRIMARY KEY AUTO_INCREMENT,
    IDcliente_FK INT,
    nome_endereco_cliente VARCHAR(100),
    nome_endereco_cnpj VARCHAR(100)
    FOREIGN KEY (IDcliente_FK) REFERENCES cliente(IDcliente_PK)
);

CREATE TABLE gerente (
    IDgerente_PK INT PRIMARY KEY AUTO_INCREMENT,
    IDcadger INT UNIQUE,
    senha_gerente VARCHAR(100),
    nome_gerente VARCHAR(50)
);

CREATE TABLE cliente (
    IDcliente_PK INT PRIMARY KEY AUTO_INCREMENT,
    nome_cliente VARCHAR(50),
    email_cliente VARCHAR(50),,
    senha_cliente VARCHAR(100),
    CPF_pessoa INT,
    FOREIGN KEY (CPF_pessoa) REFERENCES pessoa(CPF)
);

CREATE TABLE funcionario (
    IDfuncionario_PK INT PRIMARY KEY AUTO_INCREMENT,
    IDcadfun INT UNIQUE,
    senha_funcionario VARCHAR(100),
    nome_funcionario VARCHAR(50),
    categoria VARCHAR(20),
    IDpessoa_FK INT,
    FOREIGN KEY (IDpessoa_FK) REFERENCES pessoa(IDpessoa_PK)
  
);

CREATE TABLE avaliacao(
    IDavaliacao INT PRIMARY KEY AUTO_INCREMENT,
    nota INT,
    IDfuncionario_FK INT,
    FOREIGN KEY (IDfuncionario_FK) REFERENCES funcionario(IDfuncionario_PK)

)