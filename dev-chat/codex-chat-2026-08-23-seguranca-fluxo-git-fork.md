# Segurança do fluxo Git em repositórios derivados

Data: 23 de agosto de 2026.

## Pedido do usuário

Verificar como impedir que customizações locais de um fork sejam enviadas
acidentalmente ao repositório original, definir o tratamento de conflitos com
atualizações recebidas e registrar orientações reutilizáveis no `AGENTS.md`.

## Verificações

- `origin` aponta para o fork do usuário.
- O GitHub identifica o projeto como fork de um repositório original.
- A conta consultada possui somente leitura no repositório original.
- No momento da verificação, as branches padrão do fork e do original estavam
  idênticas.
- A árvore de trabalho já continha alterações não relacionadas; elas foram
  preservadas e excluídas deste escopo.

## Decisões

- As regras usam os papéis genéricos `origin` e `upstream`, sem fixar nomes de
  proprietários, projetos ou URLs.
- A branch padrão do fork deve permanecer como espelho do original.
- Customizações devem permanecer em branches de trabalho publicadas no fork.
- Push ou contribuição ao repositório original exige solicitação explícita do
  usuário.
- Atualizações devem avançar a branch padrão por fast-forward antes de serem
  integradas às branches customizadas.
- Conflitos devem ser resolvidos intencionalmente, validados e registrados.

## Alterações realizadas

- Adicionada ao `AGENTS.md` a seção `Repositórios derivados (forks)`.
- Criada a branch de documentação `docs/fork-git-safety`.

## Validações

- Revisão do estado, dos remotes, das branches e do rastreamento local.
- Comparação das branches padrão no GitHub.
- Verificação de whitespace do `AGENTS.md` sem erros.

## Dívida técnica

Nenhuma dívida técnica foi introduzida, pois a entrega altera somente a
documentação operacional do projeto.
