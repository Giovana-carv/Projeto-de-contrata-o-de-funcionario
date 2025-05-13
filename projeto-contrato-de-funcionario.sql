USE project_servico;


CREATE TABLE pessoa (
    IDpessoa_PK INT PRIMARY KEY AUTO_INCREMENT,
    senha_pessoa VARCHAR(100),
    IDendereco_FK INT,
    CPF VARCHAR(11) UNIQUE,
    nome VARCHAR(20),
    cnpj VARCHAR(20) UNIQUE
    FOREIGN KEY (IDendereco_FK) REFERENCES endereco(IDendereco_PK),
    
);

CREATE TABLE endereco (
    IDendereco_PK INT PRIMARY KEY AUTO_INCREMENT,
    nome_endereco_pessoa VARCHAR(100),
    nome_endereco_cnpj VARCHAR(100)
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
    email_cliente VARCHAR(50),
    IDcadcli INT UNIQUE,
    senha_cliente VARCHAR(100),
    IDpessoa_FK INT,
    FOREIGN KEY (IDpessoa_FK) REFERENCES pessoa(IDpessoa_PK)
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