CRUD

// Regras:
- Cada vez que você soliciata a criação de um modelo,
  ele o fará buscando o nome da tabela no banco pelo nome
  da classe. Você pode alterar o nome da tabela pela sua 
  diretiva @model-table

  Isso também ocorre com os atributos da tabela que são 
  identificados pelo nome da propriedade da classe. Porém, 
  para alterá-lo basta inserir diretiva @model-attr

- todos os tipos das propriedades da classe serão seguidos
  pelo tipo do atributo da tabela. Então, se uma propriedade
  na classe for um inteiro e o atributo na tabela for uma
  string, a propriedade da classe será transformada para um
  tipo string.

- O join é executado com a diretiva @model-join que verifica 
  se a classe existe e gera uma ou mais instancias da classe 
  dependente. Esta classe tem que ser filha da classe Model 
  para ser executada. 

- Para gerar mais de uma instancia é necessário criar o atributo 
  da classe como array

- Para limitar a quantidade de instancias é necessário colocar o
  número dentro do array. Por exemplo:
  public $property = [5]

- Muito importante! Todas as tabelas do banco usadas no model
  precisam ter uma chave primária auto increment para fazer 
  delete ou update


// Diretivas
@model-table: nome_da_tabela
- cadastra o nome da tabela que será usado

@model-attr: atributo_da_tabela
- cadastra o nome do atributo que será usado no tr

@model-join: nome\da\classe
- avisa que determinado atributo é um objeto de uma classe


Feature:

@model-foreign: nome_do_atributo_que_liga_ao_estrangeiro
- se você não tribuir, Model irá supor que o id é id_tabela_foreign

@model-primary: nome_do_atributo_primario
- se você não tribuir, Model irá supor que o id é id