# Achados de UI/UX — site e painel (29/10/2026)

Levantamento feito antes de começar a melhoria de design. Fotos tiradas com o Edge em modo sem interface (tema escuro, que é o padrão do site), em 1440px e 390px de largura, mais prints do painel enviados pelo Carlos.

## Site público (mais grave primeiro)

1. **Celular "vaza" para a direita** — conteúdo passa da largura da tela: textos cortados, botão "Doar" do topo cortado, formulário de voluntário cortado. Suspeita: elementos decorativos/posicionados fora da tela (ex.: textos "Mais inclusão / Mais oportunidades / Mais vidas" do topo da página inicial) ou larguras fixas. É o mais urgente (famílias acessam pelo celular).
2. **Textos invisíveis no tema escuro** — texto branco sobre fundo branco em faixas claras:
   - Página inicial: título "Mais que assistência…" (seção Quem somos) e título "Histórias que inspiram".
   - Voluntariado: título "Vamos conversar?" e texto da caixinha "Li e aceito a política de privacidade".
   - Botões rosa com texto rosa: "Quero ser voluntário" (Serviços) e "Doar Agora" do menu.
   Causa provável: cores escritas tela por tela em vez de uma paleta central (tokens) para claro/escuro.
3. **Fotos com rostos de crianças reconhecíveis** — foto principal da página inicial e da página Doar (entre outras em `inter-ong/src/assets/real-*.jpg`). Precisa de autorização dos responsáveis (LGPD art. 14) ou trocar/borrar.
4. **Depoimentos que parecem inventados** na página inicial ("Mãe de atendido", "Voluntário", "Parceiro da ONG" com avatares genéricos) — prejudica a credibilidade, que é o objetivo principal do site.
5. **Páginas sem padrão visual** — Serviços, Privacidade, bloco da newsletter e rodapé ficaram simples, sem o visual da página inicial.
6. Menores: menu do computador cheio (9 itens + tema + Doar); grande espaço vazio no fim de páginas curtas; selo "Pix Instantâneo" na doação conflita com a confirmação manual.

## Painel da gestão

1. **Cartão de voluntário sem destaque para o nome** (e nome/e-mail/celular vazios em cadastros antigos do banco local, que guardavam o nome no apoiador) — precisa de nome no topo e texto reserva "(sem nome)". "Idade: — anos" quando falta data.
2. **"Protocolo:" vazio** em doações antigas (anteriores ao protocolo) — some no banco novo, mas mostrar "—".
3. **Sem visão geral** — não há resumo (pendentes, valor do mês, voluntários em análise); pessoa leiga precisa clicar aba por aba.
4. **Sem identidade da ONG** — sem logo/cores; tudo branco com muito espaço vazio.
5. Falta busca (nome/protocolo); "Excluir" com o mesmo peso visual das ações do dia a dia; formulário de Conteúdos longo (botão de publicar só rolando).

## Proposta para a direção do design (a confirmar com a equipe)

Sensação nos primeiros segundos: **"acolhedor e sério"** — acolhedor para as famílias (linguagem simples, cores suaves, fácil no celular) e sério para doadores/parceiros (transparência, informação real, nada inventado).
