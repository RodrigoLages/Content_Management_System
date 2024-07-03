# Projeto Laravel

Este projeto é um simples repositório para gerenciar postagens com seus respectivos titulos, autores, conteúdos e tags.

## Funcionalidades

-   CRUD básico de posts.
-   Relacionamento m:n com tags.

## Como Executar

### Pré-requisitos

Certifique-se de ter o Docker e o Docker Compose instalados na sua máquina.

### Passos para Executar

1. **Clonar o Repositório**

Clone o repositorio usando o link do github para a sua máquina ou baixe os arquivos

2. **Configurar Variáveis de Ambiente**

Renomeie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente necessárias, como as credenciais do banco de dados.

3. **Construir e Iniciar os Contêineres Docker**

```bash
docker-compose up --build
```

3. **Executar as Migrações do Banco de Dados**

```bash
docker-compose exec main php artisan migrate
```

4. **Acessar a Aplicação**

A aplicação estará disponível em http://localhost:8000.
