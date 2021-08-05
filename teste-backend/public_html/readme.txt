# Instruções

- Utilizar o template do Materialize, editando preferencialmente apenas os arquivos proprios pra customização,
por exemplo, os arquivos 'app-assets/js/custom/custom-scritps.js' e 'app-assets/css/custom/custom.css';

# Tarefas

1) Implementar a função \Nerdweb\Database->updatePrepared() - OK

2) Criar as funções de CRUD para Notícias no namespace Nerdweb: os campos necessários pra uma notícia são [id, data, url_noticia, titulo, conteudo]; - 

3) Utilizar o template do Materialize para implementar a interface de leitura de notícias em uma tabela com os campos [id, titulo, data, url_noticia]; -

4) Implementar no template a adição, edição e deleção de elementos da lista de notícias. - 

# Bônus

1) Implementar os blocos ( head, header, footer, conteudo ) como templates utilizando a classe \Nerdweb\Template, extendendo a classe se necessário; - 

2) Alterar a classe \Nerdweb\Database para utilizar SQLite, porém mantendo compatibilidade com codigos existentes ( dica: eh necessario somente alterar o parametro args no construtor e a funcao connect() ) - 

