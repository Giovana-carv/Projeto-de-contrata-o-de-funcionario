USE project_servico;


CREATE TABLE pessoa (
    IDpessoa_PK INT PRIMARY KEY AUTO_INCREMENT,
    senha_pessoa VARCHAR(100),
    CPF VARCHAR(11) UNIQUE,
    nome VARCHAR(20),
    cnpj VARCHAR(20),
    FOREIGN KEY (IDcnpj_FK) REFERENCES cnpj(IDcnpj_PK)
);

CREATE TABLE endereco (
    IDendereco_PK INT PRIMARY KEY AUTO_INCREMENT,
    nome_endereco VARCHAR(100)
);

CREATE TABLE gerente (
    IDgerente_PK INT PRIMARY KEY AUTO_INCREMENT,
    senha_gerente VARCHAR(100),
    nome_gerente VARCHAR(50),
    senha VARCHAR(100)
);

CREATE TABLE cliente (
    IDcliente_PK INT PRIMARY KEY AUTO_INCREMENT,
    nome_cliente VARCHAR(50),
    IDendereco_FK INT,
    senha_cliente VARCHAR(100),
    IDpessoa_FK INT,
    IDgerente_FK INT,
    FOREIGN KEY (IDendereco_FK) REFERENCES endereco(IDendereco_PK),
    FOREIGN KEY (IDpessoa_FK) REFERENCES pessoa(IDpessoa_PK),
    FOREIGN KEY (IDgerente_FK) REFERENCES gerente(IDgerente_PK)
);

CREATE TABLE funcionario (
    IDfuncionario_PK INT PRIMARY KEY AUTO_INCREMENT,
    senha_funcionario VARCHAR(100),
    nome_funcionario VARCHAR(50),
    categoria VARCHAR(20),
    IDpessoa_FK INT,
    IDgerente_FK INT,
    FOREIGN KEY (IDpessoa_FK) REFERENCES pessoa(IDpessoa_PK),
    FOREIGN KEY (IDgerente_FK) REFERENCES gerente(IDgerente_PK)
);

CREATE TABLE avaliacao(
    IDavaliacao INT PRIMARY KEY AUTO_INCREMENT,
    nota INT,
    IDfuncionario_FK INT,
    FOREIGN KEY (IDfuncionario_FK) REFERENCES funcionario(IDfuncionario_PK)

)