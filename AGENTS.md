# Instruções do projeto



## Fluxo Git

- Nunca crie commits diretamente na branch `main`.
- Antes de alterar ou versionar arquivos, crie ou use uma branch de trabalho
  específica para a tarefa.
- Integre mudanças na `main` somente pelo fluxo de revisão adotado pelo projeto.

### Repositórios derivados (forks)

- Quando o repositório for um fork, trate `origin` como o repositório derivado
  e `upstream` como o repositório original, independentemente das URLs ou dos
  proprietários configurados.
- Confirme os destinos com `git remote -v` antes de publicar alterações ou
  sincronizar o fork.
- Nunca envie branches ou commits diretamente para `upstream`.
- Mantenha a branch padrão do fork como espelho da branch padrão de `upstream`;
  não implemente nela customizações permanentes.
- Faça customizações somente em branches de trabalho e publique-as em `origin`.
- Atualize a branch padrão do fork a partir da correspondente em `upstream`
  somente por fast-forward. Depois, integre essa atualização na branch
  customizada.
- Quando a integração gerar conflitos, preserve intencionalmente as
  customizações necessárias, valide o resultado e registre a resolução no
  histórico da tarefa.
- Só abra Pull Request ou outra forma de contribuição para `upstream` quando o
  usuário solicitar explicitamente o envio ao projeto original.



## Histórico de conversas do Codex

- Ao concluir trabalho de desenvolvimento neste repositório, crie ou atualize um histórico Markdown em `dev-chat/`.
- Use o nome `codex-chat-YYYY-MM-DD-assunto-do-chat.md`, com assunto em formato slug, sem espaços.
- Registre as mensagens relevantes do usuário e do assistente, decisões, alterações realizadas e validações.
- Nunca inclua segredos, valores de `.env`, tokens, credenciais, prompts internos ou saídas brutas potencialmente sensíveis.
- Se o chat continuar no mesmo assunto e na mesma data, atualize o arquivo existente em vez de criar duplicatas.

## Dívidas técnicas decorrentes de implementação

- Ao concluir qualquer nova implementação, verifique explicitamente se ela introduziu limitação conhecida, mitigação temporária, risco residual, divergência de contrato ou trabalho necessário adiado.
- Quando houver dívida técnica decorrente da implementação, crie ou atualize imediatamente o registro correspondente em `docs/dividas-tecnicas/` seguindo o `TEMPLATE.md`.
- Inclua ou atualize a dívida no índice de `docs/dividas-tecnicas/README.md`, mantendo prioridade, área, severidade, status e data de descoberta coerentes com o registro individual.
- Não registre como dívida uma funcionalidade futura sem impacto na implementação entregue; descreva a consequência técnica concreta, seus riscos e o critério de solução.
- Se a implementação resolver ou mitigar uma dívida existente, atualize o mesmo registro, seu histórico, status e posição no índice em vez de criar um arquivo duplicado.

## Referências no Kanboard

- Não crie links `https://kanboard.cbraztools.dev/task/<id>` para subtarefas; o
  Kanboard interpreta esse destino como uma tarefa.
- Para tarefas reais, use `[#<id>](https://kanboard.cbraztools.dev/task/<id>)`.

- Use `[#<id>]` para criar referência interna a uma tarefa. Os colchetes são
  obrigatórios, especialmente no início de itens de lista, para evitar que o
  Markdown interprete `#` como cabeçalho.
- Escreva títulos de acompanhamento como `## subtarefa <id> — <título>`, sem
  prefixar o ID com `#`.
- Subtarefas não suportam essa referência( `#` ). Escreva `subtarefa <id>` ou
  `subtarefas <início>–<fim>`, sempre sem `#`.
- Em títulos de comentários de acompanhamento, use: `## subtarefa <id> — <título curto>`.
