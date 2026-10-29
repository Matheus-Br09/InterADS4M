# Melhoria de design do site e do painel — design

Data: 29/10/2026. Base: `docs/superpowers/notes/2026-10-29-achados-ui-ux.md` (levantamento com fotos do site em 1440px e 390px e prints do painel).

## Intenção e porquê

O site da ONG SOS Tudo pelo Social (autismo) pede doações e cadastro de voluntários. Quem chega é de dois tipos: famílias (quase sempre pelo celular) e doadores/parceiros (que decidem se confiam). A sensação nos primeiros segundos deve ser **"acolhedor e sério"**: acolhedor para as famílias (linguagem simples, fácil no celular), sério para doadores (informação real, nada inventado, tudo funcionando).

Hoje há defeitos que quebram essa sensação: o celular "vaza" para a direita, há textos brancos sobre fundo branco no tema escuro, páginas com visuais diferentes entre si e um painel de gestão sem resumo nem identidade.

O dono do projeto (pessoa não programadora) decidiu as direções abaixo. Tudo que não está na seção "Decidido" é palpite de quem escreveu e está marcado como tal.

## Metas

- O site funciona sem cortes em celular (390px) e computador (1440px), nos dois temas.
- Nenhum texto some por falta de contraste, em nenhum tema.
- Todas as páginas públicas seguem o mesmo visual da página inicial.
- O painel dá uma visão geral logo na entrada e é fácil de usar por pessoa leiga.

## Não metas

- Mudar a paleta de cores ou o tema padrão (decidido: fica como está).
- Reescrever textos, trocar fotos ou criar conteúdo novo.
- Resolver os depoimentos (ver "Pendências").
- Novas funcionalidades além das listadas (nada de gráficos complexos, exportações etc.).
- Trocar a tecnologia do site ou adicionar bibliotecas novas sem explicar o motivo ao dono.

## Antimetas (seria fracasso mesmo "funcionando")

- Um site mais bonito que esconde informação real ou parece inventado.
- Um painel que a gestão precisa reaprender do zero.
- Qualquer botão de excluir com o mesmo destaque das ações do dia a dia.

## Decidido pelo dono

1. **Sensação:** "acolhedor e sério".
2. **Cores:** manter o tema escuro atual (azul quase preto `#090d16` e rosa `#fb2782`, definidos em `inter-ong/src/index.css`). Não adotar o azul vivo do cartaz.
3. **Fotos:** mantêm-se as fotos do Instagram da ONG, inclusive as com rostos de crianças. O dono entende que há autorização. A confirmação formal com a ONG fica como pendência (LGPD art. 14), sem travar o trabalho.
4. **Depoimentos:** ficam como estão, "de fachada", por enquanto.
6. **Selo "Pix Instantâneo":** pode ser trocado por uma frase que não prometa rapidez (ex.: "Doação por Pix"). É a única mudança de texto autorizada; o resto continua sem reescrita.
5. **Painel — entrada:** três números de resumo no topo (doações para confirmar, voluntários em análise, valor recebido no mês), as abas atuais (Doações, Voluntários, Conteúdos) logo abaixo, caixa de busca por nome ou protocolo, nome da pessoa em destaque no cartão, e "Excluir" como link discreto, longe do botão principal ("Confirmar").

## Palpites de quem escreveu (o dono ainda não confirmou)

- **Ordem do trabalho:** (1) defeitos graves do site: celular que vaza e textos invisíveis; (2) padronizar páginas simples (Serviços, Privacidade, newsletter, rodapé) no visual da página inicial; (3) painel; (4) acabamento (menu do computador com 9 itens, espaço vazio no fim de páginas curtas).
- **Cores centralizadas:** corrigir o texto invisível passando todas as telas a usar as variáveis de cor do `index.css` (hoje algumas telas, como `pages/css/Voluntariado.css`, definem as próprias cores).
- **Dados faltando no painel:** mostrar "(sem nome)" quando não houver nome, "—" para protocolo vazio e para idade sem data de nascimento.
- **Selo "Pix Instantâneo"** na página de doação conflita com a confirmação manual feita pela gestão; sugestão: trocar por um texto que não prometa instantaneidade.
- **Menu do computador:** reduzir o número de itens visíveis, agrupando os menos usados. Quais agrupar fica a cargo de quem construir.

## Restrições

- Trabalhar em um branch novo (não direto no `main`); commits pequenos, um por mudança lógica; sem commit/push sem o dono pedir.
- Antes de dizer que algo está pronto, testar de verdade: abrir o site em 390px e 1440px, nos dois temas, e o painel. Se algo não puder ser testado, dizer isso.
- Não ler nem mostrar `.env`. O PHP só roda via PowerShell (Herd); ver memória do projeto sobre o ambiente local.
- Explicar mudanças ao dono em português simples, sem jargão.
- A pasta `.superpowers/` (telas de exemplo) não entra em commits.

## Fatos do projeto (conferidos no código em 29/10/2026)

- O painel faz parte do mesmo front-end React (`inter-ong/src/pages/gestao/`: Doações, Voluntários, Conteúdos, Gestores) e compartilha o tema/CSS do site. O backend é Laravel (`backend/`).
- As listas do painel são paginadas **no servidor**, 20 por página (`GestaoDoacoesController`, `VoluntarioApiController`). Contar ou filtrar só no front-end mostraria números e buscas errados.
- Hoje não existe busca nem endpoint de resumo. Os dois exigem trabalho no backend (parâmetro de busca nas listas e um endpoint novo de resumo). Já existe um `DashboardTesteController` com somas, que serve só de referência (é de teste).
- `valor` das doações é numérico (`decimal`). Doações têm `status` (`pendente`, `concluido`, `cancelado`, `expirado`) e `data_doacao`; **não há** coluna de data de confirmação.
- Existe um guia para a gestão em `docs/guia-painel-gestao.md`, que deve ser atualizado se o painel mudar.

## Decisões de quem escreveu (resolvidas na revisão; o dono pode mudar)

- **"Valor recebido no mês":** soma de `valor` das doações com status `concluido` cuja `data_doacao` cai no mês corrente. Sem migração nova. Calculado no servidor.
- **Os três números do resumo** vêm de um endpoint novo no servidor, nunca da página carregada.
- **Busca:** feita no servidor, por nome e protocolo, nas abas Doações e Voluntários (Conteúdos não precisa).
- **Contraste:** mínimo WCAG AA (4.5:1 para texto comum). Os dois temas (claro e escuro) continuam existindo; o escuro é o padrão. Telas novas e o painel funcionam nos dois.
- **Aceite do celular:** nenhuma página pública nem do painel com rolagem horizontal em 390px, conferido com o Edge sem interface, como no levantamento. Testes automáticos existentes (`backend/tests`) devem continuar passando; para o resumo e a busca, escrever testes novos.
- **Páginas públicas a padronizar:** todas as que existem em `inter-ong/src/pages/`, com prioridade para as citadas no levantamento (Serviços, Privacidade, newsletter, rodapé). Quem construir faz o inventário.
- "Excluir" discreto mantém a confirmação que já existir; se não houver, adicionar uma.

## Pendências (não bloqueiam)

- **Depoimentos reais**: hoje são de fachada. Antes de divulgar o site para doadores, trocar por depoimentos reais com nome e autorização, ou esconder a seção. O dono vai decidir depois.
- **Autorização das fotos**: pedir à ONG uma confirmação simples por mensagem.

## O que fica a cargo de quem construir

- Estrutura exata das telas, espaçamentos, tamanhos de letra e detalhes visuais, respeitando o que está decidido.
- Como tornar o site à prova de "vazar" no celular (descobrir a causa real: elementos fora da tela ou larguras fixas).
- Divisão em etapas e testes.
