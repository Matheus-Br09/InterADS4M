# MVP de lançamento — site da ONG SOS Tudo pelo Social

**Data:** 29/10/2026
**Branch:** `mvp-lancamento`
**Base:** documento acadêmico "Plano de Gerenciamento do Projeto Interdisciplinar 2026.2" (UNIBRA) + código atual do repositório.

---

## 1. Intenção e porquê

A SOS Tudo pelo Social (Paulista-PE) atende pessoas neurodivergentes com neuropedagogia, nutrição, odontologia e fisioterapia. O site começou como projeto acadêmico e agora vai **para produção de verdade**, usado pela ONG.

O objetivo central (do documento de requisitos): **dar credibilidade e transparência à ONG** para facilitar a captação de recursos e parcerias. Secundários: atrair voluntários, apresentar os serviços e disponibilizar materiais educativos para as famílias.

A estratégia é lançar um MVP pequeno e confiável e melhorar com o tempo. A manutenção fica com a equipe de alunos; a ONG paga a hospedagem.

## 2. Objetivos (entra no MVP)

### Visitante (sem login em nada)
1. **Páginas institucionais** — história, missão, valores, endereço, galeria de fotos (já existem em `Sobre`, `LandingPage`, `Contato`).
2. **Página de serviços (nova)** — apresenta neuropedagogia, nutrição, odontologia e fisioterapia. Textos iniciais podem ser genéricos e marcados para a ONG revisar.
3. **Notícias, Transparência (PDFs) e Área Educacional** — já existem; manter.
4. **Doação Pix anônima** — escolher valor, ver chave Pix + QR Code, copiar chave, receber protocolo. Nome/e-mail do doador **opcionais**. Registro entra como **pendente** até a gestão confirmar manualmente olhando o extrato.
5. **Cadastro de voluntário anônimo** — nome, e-mail, celular, **data de nascimento (bloquear < 18 anos no frontend E no backend)**, área de atuação, disponibilidade, currículo PDF opcional, **checkbox obrigatório de aceite da política de privacidade**.
6. **Newsletter** — já existe; adicionar aceite de privacidade.
7. **Botões de WhatsApp e Instagram** — Instagram já existe; WhatsApp será fornecido depois pela equipe (deixar configurável).
8. **Política de privacidade** — página simples (quais dados coleta, para quê, quem acessa, como pedir exclusão, contato).
9. **Painel de acessibilidade flutuante** (RF09) — aumentar/diminuir fonte, alto contraste, preferência salva no navegador. Escolha minha (Claude) por estar marcado "Importante" no documento e combinar com o público.

### Gestão da ONG
10. **Painel enxuto** — somente: confirmar/cancelar doações pendentes, listar e mudar status de voluntários (e baixar currículo), CRUD de notícias, materiais educativos e documentos de transparência, criar/desativar logins de gestor.
11. **Aviso por e-mail** à gestão quando entrar doação ou voluntário (e-mail da própria Hostinger via SMTP).
12. **Guia de uso do painel** — 1–2 páginas com prints, em linguagem para quem não é de tecnologia. Quem vai usar o painel ainda **não está definido**; assumir usuário leigo.

## 3. Não-objetivos (fica para depois)

- Login/cadastro de apoiador e área "minha conta" (no site e no Blade).
- Apadrinhamento de crianças e lista pública de crianças.
- Doação mensal/recorrente, cartão, gateway, confirmação automática de Pix.
- Refatorações que não afetam o lançamento (ex.: dividir `DashboardTesteController` além do necessário).
- Animações de avatares (RNF "interatividade lúdica").

O código dessas funções pode continuar no repositório, mas **as rotas e links ficam desligados/ocultos** em produção.

## 4. Anti-objetivos (falha mesmo que "funcione")

- **Chave Pix errada** no ar → dinheiro de doador vai para outra pessoa.
- **Dados expostos**: voluntários, currículos, doadores ou crianças acessíveis sem login de gestor. Hoje `GET /api/criancas` é público com nome, foto e idade — **deve ser removido/desligado**.
- **Dados reais de teste em produção**: o banco local e `storage/logs/laravel.log` contêm CPF e hash de senha de pessoas reais (ver `relatorio_backend.md` §5). Produção começa com banco **limpo** (só migrations + 1 gestor criado por comando), sem seeders de demonstração, sem copiar `storage/`.
- **Fotos com crianças identificáveis** no site sem autorização registrada dos responsáveis (LGPD art. 14). Usar fotos com rostos borrados (como nos anexos do documento) ou genéricas.
- **Site fora do ar / lento** no momento de doar.
- **ONG dependente dos alunos** para operar o dia a dia (painel tem que ser usável sozinho).
- Botões ou dados de teste visíveis ("seed-dados", "DashboardTeste", textos "intenção de apoio vinculada à sua conta").

## 5. Restrições

- **Hospedagem: Hostinger** (plano compartilhado com PHP + MySQL + SSH; escolher o plano que suporte a versão de PHP exigida pelo `composer.json`). Paga pela ONG.
- **Domínio `.org.br` no nome/CNPJ da ONG** (Registro.br, ~R$ 40/ano), não no de um aluno.
- **Stack existente mantida:** Laravel 13 / PHP 8.4 / MySQL 8 no backend; React 19 + Vite + Tailwind no frontend. Sem bibliotecas novas sem justificativa.
- **Sem segredos no código:** chave Pix, número do WhatsApp, e-mail da gestão, SMTP e CORS vêm de configuração (`.env` do backend; o frontend busca chave Pix/WhatsApp de um endpoint público de configuração ou de `VITE_*` no build — decisão do builder).
- **Prontuários/dados de saúde:** o site não armazena (RNF do documento).
- **Sem data fixa** de lançamento.
- Trabalho em branch separado; commits pequenos; nada de push sem pedir.

## 6. Decisões já tomadas

| Decisão | Quem decidiu |
|---|---|
| Hostinger, paga pela ONG | Equipe |
| Manutenção pela equipe | Equipe |
| Doação e voluntariado sem login | Equipe (MVP-SIMPLES.md) + confirmado nesta conversa |
| Apadrinhamento, login de apoiador e mensalidade fora do MVP | Proposta do Claude, aprovada pela equipe |
| Pix com confirmação manual pela gestão | MVP-SIMPLES.md |
| Chave Pix: a equipe pega no Instagram da ONG; **confirmar diretamente com a ONG** antes do lançamento | Equipe + recomendação do Claude |
| WhatsApp: número enviado depois | Equipe |
| Painel de acessibilidade no MVP | Claude |
| Avisos por e-mail via conta da Hostinger | Claude |
| **Conciliação do Pix:** o protocolo vai no campo `txid` do BR Code (aparece no extrato em muitos bancos); a tela de doações mostra protocolo, valor, data/hora e nome (se informado), com filtro por status. Pendentes viram `expirada` automaticamente após 7 dias (comando agendado no cron da Hostinger); a gestão pode confirmar mesmo uma expirada | Claude |
| **Números de apoiadores na página inicial:** os blocos de "total de apoiadores / doadores mensais / únicos" (`LandingPage.jsx`, `GET /api/v1/estatisticas`) ficam **ocultos** no MVP, pois mostrariam zero. Depois podem voltar com números de impacto informados pela ONG | Claude |
| **Exclusão de dados (LGPD):** o painel tem botão "excluir" para voluntário (apaga o registro e o currículo do disco) e para doação (anonimiza nome/e-mail). A política de privacidade diz que o pedido de exclusão é feito pelo e-mail de contato e atendido pela gestão no painel. Retenção: currículos de voluntários não aprovados podem ser excluídos pela gestão a qualquer momento; sem exclusão automática no MVP | Claude |

## 7. O que já existe e o que muda (para orientar o plano)

**Já funciona:** voluntariado anônimo (`POST /api/v1/voluntariado` público, `apoiador_id` nullable — commit `3bef274`); notícias/materiais/transparência com CRUD via `/api/v1/gestao/*`; listagem e status de voluntários na gestão; newsletter; CORS por `.env` (`CORS_ALLOWED_ORIGINS`); `.env.example` com `APP_DEBUG=false`.

**Lacunas conhecidas:**
- `POST /api/v1/doacoes` está dentro de `auth:apoiador` (`backend/routes/integracao.php:34`). Tabela `doacoes_unicas`: `apoiador_id` NOT NULL e `status` default `concluido` → precisa `apoiador_id` nullable, campos opcionais de nome/e-mail, protocolo, status default `pendente`.
- Gestão não tem tela/endpoint para listar e confirmar doações anônimas.
- Login de gestor: hoje o gestor é um `Apoiador` com `tipo_usuario = admin` (`EhGestor`). Com o login de apoiador fora do MVP, o builder decide se mantém esse modelo (só criando contas admin) ou usa o model `Administrador`. Requisito: só gestores conseguem logar; cadastro público desligado.
- Painel hoje é parte Blade (`DashboardTesteController`, botão `seed-dados`) e parte SPA (`GestaoMateriais.jsx`). O builder escolhe **um** lugar para o painel do MVP; remover o botão de seed e o nome "Teste" da interface.
- Voluntário: falta data de nascimento + validação 18+ e aceite de privacidade (frontend e backend).
- Chave Pix fixa em `inter-ong/src/pages/Doar.jsx:28`; QR Code é ilustrativo. Gerar QR Code Pix real ("copia e cola" estático BR Code) a partir da chave configurada — ou, se o builder preferir não gerar, mostrar só a chave copiável. Não exibir QR falso.
- `Doar.jsx` e `NavBar` ainda linkam para `/login`; remover links de login/cadastro públicos.
- `GET /api/criancas` (`backend/routes/api.php:18`) e `/api/apadrinhamentos` públicos → remover.
- Pasta duplicada `InterADS4M/` (clone aninhado, não versionado): verificada em 29/10 — código idêntico (só quebras de linha diferentes), sem mudanças pendentes, sem stash e todos os commits presentes no repositório principal. **Remoção aprovada pela equipe**; se ainda existir, apagar como primeira tarefa.
- Página de serviços, política de privacidade, botão WhatsApp e widget de acessibilidade: não existem.
- Pendências de produção do `relatorio_backend.md` §5 (alta prioridade, itens 3–5): CORS fechado, `APP_DEBUG=false`, cookies seguros sob HTTPS, `trustProxies`, cabeçalhos básicos.

## 8. Deploy (forma geral; detalhes ao builder)

**Duas fases (decisão da equipe):**
- **Fase 1 — pronto para publicar (este plano):** todo o código, testes passando, verificação local de ponta a ponta (critérios 1–6 da §9 testados em ambiente local/Docker) e o passo a passo de publicação escrito no README. Não depende de nenhuma conta da ONG.
- **Fase 2 — publicação (plano separado, quando a ONG liberar Hostinger, domínio, e-mail e chave Pix confirmada):** executar o passo a passo, validar critérios 7–8 e fazer o teste com a ONG.

- Frontend: `npm run build` → arquivos estáticos servidos pelo domínio principal.
- Backend: Laravel servindo `/api` (mesmo domínio ou subdomínio `api.` — decisão do builder; mesmo domínio simplifica cookies/CORS).
- Banco MySQL criado no painel da Hostinger; `php artisan migrate --force`; criar gestor por comando artisan.
- Currículos em disco privado (fora de `public/`).
- SSL grátis da Hostinger ativado; HTTPS obrigatório.
- **Backup**: rotina de backup do banco e de `storage/app` (o backup da Hostinger ou um cron com `mysqldump`) — escolha do builder, mas tem que existir.
- Um passo a passo de deploy escrito no README, para que outro aluno consiga republicar.

## 9. Critérios de pronto para lançar

1. Visitante doa via Pix sem login, recebe protocolo; gestão recebe e-mail, vê a doação pendente e marca como confirmada.
2. Visitante maior de 18 se cadastra como voluntário (com e sem currículo); menor de 18 é recusado no frontend e no backend; gestão recebe e-mail e vê o cadastro.
3. Nenhum endpoint público devolve dado pessoal (voluntário, doador, criança); tentar acessar rotas de gestão sem login retorna 401/403.
4. Gestor consegue publicar uma notícia e um PDF de transparência sozinho seguindo o guia.
5. Site funciona no celular; widget de acessibilidade altera fonte e contraste.
6. Testes do backend (`php artisan test`) e frontend (`npm test`, `npm run lint`) passando.
7. Chave Pix exibida confirmada pela ONG.
8. Teste com a ONG (critério de aceitação do documento acadêmico) antes do anúncio público.

## 10. Deixado para o builder

- Ordem das tarefas e divisão em commits.
- Onde fica o painel (Blade ou SPA) e qual model representa o gestor.
- Mesmo domínio vs. subdomínio para a API.
- Como o frontend recebe chave Pix / WhatsApp (endpoint de config ou variável de build).
- Biblioteca/abordagem para o BR Code do Pix (justificar se for dependência nova).
- Texto inicial da política de privacidade e da página de serviços (marcar "revisar com a ONG").
- Forma do backup.
