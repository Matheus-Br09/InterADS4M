# 📋 Plano MVP — Plataforma ONG SOS para Produção AWS

**Projeto:** InterADS4M — Site da ONG SOS Tudo pelo Social  
**Responsáveis:** Carlos & Matheus  
**Data:** 26/10/2026  
**Objetivo:** Colocar a plataforma no ar de forma segura e sustentável

---

## 📊 SUMÁRIO EXECUTIVO

### Status Atual
- ✅ **Backend Laravel**: 243 testes aprovados, autenticação segura, APIs funcionais
- ✅ **Frontend React**: Estrutura completa, páginas de doação e cadastro
- ⚠️ **Integração**: Frontend e backend NÃO estão conectados (prioridade #1)
- ⚠️ **Pagamentos**: Sistema registra "intenção" mas não cobra (precisa gateway)
- ⚠️ **Email**: Confirmação de cadastro não implementada (precisa Resend)

### Investimento Necessário
- **Infraestrutura AWS**: R$ 175-400/mês (~$35-80/mês)
- **Fundo de Reserva Inicial**: R$ 1.500-2.000 (primeiros 3-4 meses)
- **Total Ano 1**: ~R$ 3.600-6.000 (com Free Tier AWS)

### Mensalidade Recomendada
- **Apoio Mensal**: R$ 40/mês
- **Apadrinhamento**: R$ 70/mês
- **Meta Mínima**: 15 apoiadores = R$ 600/mês (cobre custos)
- **Meta Ideal**: 50 apoiadores = R$ 2.500/mês (sustentável + sobra para ONG)

---

## 🎯 PARTE 1: ANÁLISE DE CUSTOS OPERACIONAIS

### 1.1 Custos de Infraestrutura Digital

#### **Hospedagem AWS (Mensal)**

| Componente | Especificação | Custo Mensal (USD) | Custo Mensal (BRL) |
|------------|---------------|--------------------|--------------------|
| **EC2** (Servidor Backend) | t3.micro (2 vCPU, 1GB RAM) | $7-10 | R$ 35-50 |
| **RDS MySQL** (Banco de Dados) | t3.micro (20GB) | $15-20 | R$ 75-100 |
| **S3** (Armazenamento Frontend) | 5GB + transferência | $1-2 | R$ 5-10 |
| **CloudFront** (CDN + HTTPS) | 50GB tráfego/mês | $1-3 | R$ 5-15 |
| **Route 53** (DNS) | 1 zona hospedada | $0.50 | R$ 3 |
| **ALB** (Load Balancer) | Opcional | $16 | R$ 80 |
| **Backup/Snapshots** | RDS automático | $2-5 | R$ 10-25 |
| **SUBTOTAL** | | **$27-56** | **R$ 135-280** |

**Observações:**
- **Free Tier AWS (1º ano)**: 750h/mês EC2 + 750h/mês RDS = economia de ~$20/mês
- **ALB é opcional**: Para MVP inicial, podem usar IP Elástico (gratuito)
- **Custo Real Estimado Ano 1**: R$ 150-200/mês (com Free Tier)
- **Custo Real Após Ano 1**: R$ 350-450/mês (sem Free Tier)

---

#### **Serviços Complementares (Mensal)**

| Serviço | Finalidade | Plano Gratuito | Custo Pago |
|---------|-----------|----------------|------------|
| **Resend** | Email transacional | 3.000 emails/mês | $20/mês (50k emails) |
| **Mercado Pago** | Gateway de pagamento | - | 4.99% + R$0.40/transação |
| **Domínio .com.br** | Registro anual | - | R$ 40/ano (~R$3/mês) |
| **Certificado SSL** | HTTPS | GRÁTIS (AWS ACM) | R$ 0 |
| **SUBTOTAL** | | **R$ 3/mês** | **R$ 23/mês** |

**Taxa de Pagamento (Variável):**
- Se receberem R$ 1.000 em doações: R$ 50 de taxa (5%)
- Se receberem R$ 5.000 em doações: R$ 250 de taxa (5%)

---

### 1.2 Custos Ocultos e Imprevistos

| Item | Descrição | Custo Estimado |
|------|-----------|----------------|
| **Suporte Técnico** | Ajustes, bugs, atualizações | R$ 200-500/mês |
| **Manutenção** | Atualizações de segurança Laravel/React | R$ 100-200/mês |
| **Contingência** | Aumento de tráfego, problemas inesperados | R$ 100/mês |
| **SUBTOTAL** | | **R$ 400-800/mês** |

**IMPORTANTE:** Vocês sendo iniciantes na área, vão precisar de suporte técnico externo nos primeiros 6 meses.

---

### 1.3 Resumo Financeiro Total

| Cenário | Mês 1-12 (com Free Tier) | Após Ano 1 |
|---------|--------------------------|------------|
| **Mínimo (sem suporte)** | R$ 150-200/mês | R$ 350-450/mês |
| **Realista (com suporte eventual)** | R$ 400-500/mês | R$ 600-800/mês |
| **Conservador (margem segurança)** | R$ 600-700/mês | R$ 800-1.000/mês |

**Recomendação:** Planejar para **R$ 500/mês** no primeiro ano.

---

## 💰 PARTE 2: ESTRATÉGIA DE PRECIFICAÇÃO

### 2.1 Análise de Viabilidade

#### **Ponto de Equilíbrio (Break-even)**

**Cenário Conservador (Custo R$ 500/mês):**

| Mensalidade | Apoiadores Necessários | Receita Gerada | Sobra |
|-------------|------------------------|----------------|-------|
| R$ 20/mês | 25 pessoas | R$ 500 | R$ 0 |
| R$ 30/mês | 17 pessoas | R$ 510 | R$ 10 |
| R$ 40/mês | **13 pessoas** | R$ 520 | R$ 20 |
| R$ 50/mês | 10 pessoas | R$ 500 | R$ 0 |

**Conclusão:** Com **R$ 40/mês**, precisam de apenas 13 apoiadores para cobrir custos.

---

### 2.2 Modelo de Mensalidades Proposto

#### **Plano Único (Simplificado para Lançamento)**

```
💎 APOIADOR MENSAL — R$ 40/mês

O que está incluído:
✓ Acesso à área exclusiva do apoiador
✓ Relatório mensal de transparência financeira
✓ Atualizações sobre as crianças atendidas
✓ Certificado digital de apoiador
✓ Recibo para declaração de Imposto de Renda
```

**Vantagens deste modelo:**
- Simples de explicar
- Valor psicologicamente acessível (R$ 1,33/dia)
- Cobre custos com margem de segurança
- Competitivo com mercado (veja comparação abaixo)

---

#### **Plano com Apadrinhamento (Crescimento Futuro)**

Após consolidar a base inicial, adicionar:

```
💎 APOIO MENSAL — R$ 40/mês
   (Suporte geral à ONG)

💎 APADRINHAMENTO — R$ 70/mês
   (Conexão direta com 1 criança específica)
   
   O que inclui ALÉM do apoio mensal:
   ✓ Perfil da criança apadrinhada
   ✓ Foto e carta/vídeo trimestral
   ✓ Relatório personalizado de progresso
   ✓ Certificado de apadrinhamento
```

---

### 2.3 Comparativo de Mercado

| ONG | Mensalidade | Modelo | Público |
|-----|-------------|--------|---------|
| **ChildFund Brasil** | R$ 67/mês | Apadrinhamento | Nacional |
| **Compassion** | R$ 142/mês | Apadrinhamento | Internacional |
| **Aldeias Infantis SOS** | R$ 30/mês | Apoio geral | Nacional |
| **World Vision** | R$ 84/mês | Apadrinhamento | Internacional |
| **SUA ONG (proposta)** | **R$ 40/mês** | Apoio geral | Local → Nacional |
| **SUA ONG (apadrinhamento)** | **R$ 70/mês** | Apadrinhamento | Local → Nacional |

**Conclusão:** Vocês estão **competitivos** e abaixo da média do mercado de apadrinhamento (que é R$ 80-100/mês).

---

### 2.4 Projeções Financeiras (Ano 1)

#### **Cenário Conservador**

| Mês | Apoiadores | Receita Bruta | Custo AWS | Taxa Gateway (5%) | Sobra Líquida |
|-----|------------|---------------|-----------|-------------------|---------------|
| 1-3 | 5 | R$ 200 | R$ 500 | R$ 10 | **-R$ 310** (déficit) |
| 4-6 | 15 | R$ 600 | R$ 500 | R$ 30 | **R$ 70** |
| 7-9 | 30 | R$ 1.200 | R$ 500 | R$ 60 | **R$ 640** |
| 10-12 | 50 | R$ 2.000 | R$ 500 | R$ 100 | **R$ 1.400** |

**Total Ano 1:**
- Investimento necessário: R$ 930 (déficit primeiros 3 meses)
- Receita acumulada: R$ 12.000
- Custos acumulados: R$ 6.000
- **Lucro Líquido Ano 1: ~R$ 6.000**

---

#### **Cenário Otimista (Com Apadrinhamento)**

Mix de planos (50% apoio R$40 + 50% apadrinhamento R$70 = média R$55/apoiador):

| Mês | Apoiadores | Receita Bruta | Custo AWS | Sobra Líquida |
|-----|------------|---------------|-----------|---------------|
| 1-3 | 8 | R$ 440 | R$ 500 | **-R$ 60** |
| 4-6 | 20 | R$ 1.100 | R$ 500 | **R$ 600** |
| 7-9 | 40 | R$ 2.200 | R$ 500 | **R$ 1.700** |
| 10-12 | 70 | R$ 3.850 | R$ 600 | **R$ 3.250** |

**Total Ano 1: ~R$ 15.000 de receita → R$ 9.000 líquido**

---

### 2.5 Recomendação Final de Preço

#### **FASE 1 — Lançamento (Mês 1-6):**
```
💎 Mensalidade Única: R$ 40/mês
   (Foco em aquisição, simplicidade)
```

#### **FASE 2 — Consolidação (Mês 7-12):**
```
💎 Apoio Mensal: R$ 40/mês
💎 Apadrinhamento: R$ 70/mês
   (Aumenta ticket médio, diferenciação)
```

#### **FASE 3 — Crescimento (Ano 2+):**
```
💎 Apoio Mensal: R$ 45/mês (reajuste)
💎 Apadrinhamento: R$ 80/mês
💎 Apoio Corporativo: R$ 500/mês (empresas)
```

---

## 🏗️ PARTE 3: ARQUITETURA TÉCNICA AWS

### 3.1 Infraestrutura Proposta (MVP)

```
┌─────────────────────────────────────────────────────────┐
│  USUÁRIO (Navegador)                                    │
└────────────────┬────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│  Route 53 (DNS)                                         │
│  seudominio.com.br                                      │
└────────────┬────────────────────────────────────────────┘
             │
             ├─────────────────┬──────────────────┐
             ▼                 ▼                  ▼
    ┌────────────────┐  ┌─────────────┐  ┌──────────────┐
    │  CloudFront    │  │ CloudFront  │  │   EC2        │
    │  (CDN Global)  │  │  (HTTPS)    │  │  (Backend)   │
    └───────┬────────┘  └──────┬──────┘  └──────┬───────┘
            │                  │                 │
            ▼                  ▼                 ▼
    ┌────────────────┐  ┌─────────────┐  ┌──────────────┐
    │   S3 Bucket    │  │  ACM (SSL)  │  │  RDS MySQL   │
    │  (Frontend)    │  │  Gratuito   │  │  (Banco)     │
    │  React Build   │  └─────────────┘  │  8.0         │
    └────────────────┘                   └──────────────┘
```

---

### 3.2 Componentes Detalhados

#### **3.2.1 Frontend (S3 + CloudFront)**

**S3 Bucket:**
- Nome: `ong-sos-frontend`
- Conteúdo: Build estático React (`npm run build`)
- Permissões: Privado (acesso apenas via CloudFront)

**CloudFront:**
- Distribuição única
- Origem: S3 bucket acima
- HTTPS: Certificado ACM (gratuito)
- Comportamento: Cache agressivo (assets), sem cache (index.html)
- Domínio customizado: `www.seudominio.com.br`

**Custo:** ~R$ 10-20/mês (tráfego moderado)

---

#### **3.2.2 Backend (EC2 + RDS)**

**EC2 Instance:**
- Tipo: `t3.micro` (1 vCPU, 1GB RAM)
- SO: Ubuntu 24.04 LTS
- Software: PHP 8.4, Nginx, Composer
- IP Elástico: Sim (gratuito enquanto associado)
- Security Group:
  - Porta 22 (SSH): Apenas seu IP
  - Porta 80/443 (HTTP/HTTPS): 0.0.0.0/0 (público)
  - Porta 3306: Bloqueada (MySQL interno)

**RDS MySQL:**
- Tipo: `t3.micro` (1 vCPU, 1GB RAM)
- Storage: 20GB SSD (gp3)
- Backup: Automático (7 dias retenção)
- Multi-AZ: Não (para MVP)
- Security Group:
  - Porta 3306: Apenas do Security Group do EC2

**Custo:** ~R$ 130-180/mês (sem Free Tier)

---

#### **3.2.3 DNS e Domínio (Route 53)**

**Zona Hospedada:**
- Nome: `seudominio.com.br`

**Registros DNS:**
- `@` (raiz) → CloudFront (Frontend)
- `www` → CNAME para `@`
- `api` → IP Elástico do EC2
- Registros MX (email) → Resend

**Custo:** R$ 3/mês + R$ 40/ano do domínio

---

### 3.3 Serviços Complementares

#### **3.3.1 Email Transacional (Resend)**

**Configuração:**
```env
# backend/.env
MAIL_MAILER=resend
RESEND_KEY=re_sua_chave_api
MAIL_FROM_ADDRESS=noreply@seudominio.com.br
MAIL_FROM_NAME="ONG SOS"
```

**Emails a Implementar:**
1. Confirmação de cadastro (obrigatório)
2. Recuperação de senha
3. Recibo de doação mensal
4. Relatório mensal ao apoiador
5. Alerta de cartão recusado

**Custo:** Gratuito até 3.000 emails/mês (suficiente para 100 apoiadores)

---

#### **3.3.2 Gateway de Pagamento (Mercado Pago)**

**Por que Mercado Pago:**
- API bem documentada
- Suporta Pix, Cartão, Boleto
- Recorrência nativa (para mensalidades)
- Split payment (futuramente, para múltiplas ONGs)
- Sem taxa de setup
- Homologação simplificada para ONGs

**Taxas:**
- Pix: 0.99%
- Cartão crédito à vista: 4.99% + R$ 0.39
- Cartão crédito recorrente: 4.99% + R$ 0.39
- Boleto: R$ 3.99 fixo

**Alternativas:**
- **PagSeguro**: Taxas similares, mais popular
- **Asaas**: 1.99% boleto, bom para recorrência

**Integração:**
- SDK oficial PHP: `composer require mercadopago/dx-php`
- Webhook para confirmação automática
- Idempotência para evitar cobranças duplicadas

---

## 🚀 PARTE 4: ROTEIRO DE IMPLEMENTAÇÃO

### 4.1 Pré-requisitos

#### **Contas e Acessos Necessários:**

- [ ] Conta AWS (requer cartão internacional)
- [ ] Domínio registrado (.com.br ou .org.br)
- [ ] Conta GitHub/GitLab (para deploy)
- [ ] Conta Resend (email)
- [ ] Conta Mercado Pago (pagamentos)

**Tempo estimado:** 1-2 dias para criar todas as contas

---

### 4.2 Cronograma Detalhado (8 Semanas)

#### **SEMANA 1-2: Correções de Código**

**Responsável:** Carlos & Matheus  
**Prioridade:** CRÍTICA

| Tarefa | Descrição | Tempo |
|--------|-----------|-------|
| ✅ Conectar Frontend ao Backend | Substituir chamadas para `NomeDoArquivoLogin.php` por API real `/api/v1/auth/entrar` | 2 dias |
| ✅ Remover validação CPF terceiros | Usar validação backend existente (`App\Rules\Cpf`) | 1 dia |
| ✅ Configurar variáveis ambiente | Criar `.env.production` com valores corretos | 2h |
| ✅ Implementar confirmação email | Integrar Resend no backend Laravel | 1 dia |
| ⚠️ Entregar senhas rotacionadas | Executar comandos e entregar credenciais às 4 contas | 4h |
| ⚠️ Implementar gateway pagamento | Integração básica Mercado Pago (Pix + Cartão) | 3 dias |

**Entregável:** Código 100% funcional para produção

---

#### **SEMANA 3: Infraestrutura AWS (Banco de Dados)**

**Responsável:** Carlos (ou contratar suporte)  
**Prioridade:** ALTA

| Tarefa | Passos | Tempo |
|--------|--------|-------|
| Criar conta AWS | Cadastro + verificação cartão | 1h |
| Provisionar RDS MySQL | Console AWS → RDS → Create Database | 2h |
| Configurar Security Groups | Permitir conexão apenas do EC2 | 1h |
| Migrar dados | Exportar MySQL local → Importar RDS | 2h |
| Testar conexão | `mysql -h endpoint -u user -p` | 30min |

**Custo:** $0 (Free Tier primeiro ano)  
**Entregável:** Banco de dados funcional na nuvem

---

#### **SEMANA 4: Infraestrutura AWS (Servidor Backend)**

| Tarefa | Passos | Tempo |
|--------|--------|-------|
| Provisionar EC2 | t3.micro Ubuntu 24.04 | 1h |
| Instalar LEMP Stack | PHP 8.4, Nginx, Composer | 2h |
| Clonar repositório | Git clone do backend Laravel | 30min |
| Configurar `.env` | Credenciais RDS, chaves API | 1h |
| Rodar migrations | `php artisan migrate --force` | 30min |
| Configurar Nginx | Virtual host apontando para Laravel | 1h |
| Testar API | `curl http://ip-publico/api/criancas` | 30min |

**Custo:** $0 (Free Tier primeiro ano)  
**Entregável:** API Laravel respondendo na nuvem

---

#### **SEMANA 5: Infraestrutura AWS (Frontend + CDN)**

| Tarefa | Passos | Tempo |
|--------|--------|-------|
| Build de produção | `npm run build` no `inter-ong/` | 10min |
| Criar bucket S3 | `ong-sos-frontend` | 30min |
| Upload arquivos | AWS CLI ou console | 30min |
| Criar distribuição CloudFront | Apontar para S3 | 1h |
| Solicitar certificado SSL | AWS ACM (gratuito) | 30min |
| Aguardar validação DNS | 10min-24h |
| Associar domínio | CloudFront custom domain | 30min |

**Custo:** ~R$ 10/mês  
**Entregável:** Site acessível via `https://seudominio.com.br`

---

#### **SEMANA 6: DNS e Domínio**

| Tarefa | Passos | Tempo |
|--------|--------|-------|
| Registrar domínio | Registro.br ou Route 53 | 1h |
| Criar zona hospedada | Route 53 | 30min |
| Configurar registros | A, CNAME, MX | 1h |
| Apontar nameservers | No registrador para Route 53 | 30min |
| Aguardar propagação | 24-48h | - |
| Verificar domínio Resend | Adicionar registros TXT | 1h |

**Custo:** R$ 40/ano + R$ 3/mês  
**Entregável:** Domínio próprio funcionando

---

#### **SEMANA 7: Testes e Ajustes**

| Tarefa | Checklist | Tempo |
|--------|-----------|-------|
| Teste de cadastro | Criar conta → receber email → logar | 1h |
| Teste de doação | Doar via Pix → verificar webhook | 2h |
| Teste de apadrinhamento | Vincular criança → ver no painel | 1h |
| Teste de email | Todos os templates Resend | 2h |
| Teste de segurança | HTTPS, CORS, rate limiting | 2h |
| Teste de performance | Tempo de carregamento < 3s | 1h |
| Ajustes finais | Correções de bugs encontrados | 1-2 dias |

**Entregável:** Sistema testado e validado

---

#### **SEMANA 8: Lançamento e Monitoramento**

| Tarefa | Descrição | Tempo |
|--------|-----------|-------|
| Configurar CloudWatch | Alertas de CPU, memória, erros | 2h |
| Documentar acessos | Senhas, IPs, chaves API | 1h |
| Treinar equipe | Como acessar painel, relatórios | 2h |
| Lançamento soft | Compartilhar com 5-10 amigos | - |
| Ajustes pós-feedback | Correções de UX | 1-2 dias |
| Lançamento público | Redes sociais, contatos | - |

**Entregável:** Plataforma no ar e operacional

---

### 4.3 Checklist de Deploy (Copiar e Colar)

```markdown
## PRÉ-DEPLOY

### Código
- [ ] Frontend conectado ao backend (`/api/v1`)
- [ ] Validação CPF via backend (não terceiro)
- [ ] Resend configurado e testado
- [ ] Gateway pagamento integrado (sandbox)
- [ ] Testes passando: `php artisan test` (243 testes)
- [ ] Build funcional: `npm run build`

### Variáveis de Ambiente (backend/.env)
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://seudominio.com.br`
- [ ] `DB_HOST` (RDS endpoint)
- [ ] `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- [ ] `CORS_ALLOWED_ORIGINS=https://seudominio.com.br`
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `RESEND_KEY` (API key)
- [ ] `MERCADOPAGO_ACCESS_TOKEN` (produção)

### AWS
- [ ] RDS MySQL criado e acessível
- [ ] EC2 configurado com PHP 8.4 + Nginx
- [ ] S3 bucket criado
- [ ] CloudFront configurado
- [ ] Certificado SSL emitido
- [ ] Route 53 zona criada
- [ ] Security Groups corretos

### Domínio
- [ ] Domínio registrado
- [ ] Nameservers apontando para Route 53
- [ ] Registros DNS configurados
- [ ] Domínio verificado no Resend

### Segurança
- [ ] Senhas rotacionadas entregues
- [ ] Chaves SSH criadas (não reusar antigas)
- [ ] Firewall configurado (portas corretas)
- [ ] Backup RDS ativado
- [ ] HTTPS obrigatório

### LGPD/Jurídico
- [ ] Termo de autorização dos responsáveis
- [ ] Política de privacidade publicada
- [ ] Termos de uso publicados

## PÓS-DEPLOY

### Testes em Produção
- [ ] Cadastro funcionando
- [ ] Login funcionando
- [ ] Doação Pix funcionando
- [ ] Email de confirmação chegando
- [ ] Área do apoiador acessível
- [ ] HTTPS válido (sem warnings)
- [ ] Performance aceitável (< 3s)

### Monitoramento
- [ ] CloudWatch configurado
- [ ] Alertas de erro configurados
- [ ] Backup testado (restore)
- [ ] Logs funcionando

### Documentação
- [ ] Acessos documentados (senha cofre)
- [ ] Procedimentos de emergência
- [ ] Contatos de suporte
```

---

## 📈 PARTE 5: ESTRATÉGIA DE CRESCIMENTO

### 5.1 Metas Trimestrais (Ano 1)

#### **Q1 (Mês 1-3): Validação**
- **Meta Apoiadores:** 10
- **Receita:** R$ 400/mês
- **Foco:** Testar sistema, corrigir bugs, feedback inicial
- **Marketing:** Amigos, familiares, parceiros próximos

#### **Q2 (Mês 4-6): Crescimento Inicial**
- **Meta Apoiadores:** 25
- **Receita:** R$ 1.000/mês (break-even)
- **Foco:** Redes sociais, eventos locais, parcerias
- **Marketing:** Instagram, Facebook, WhatsApp

#### **Q3 (Mês 7-9): Aceleração**
- **Meta Apoiadores:** 50
- **Receita:** R$ 2.000/mês
- **Foco:** Lançar plano de apadrinhamento
- **Marketing:** Anúncios pagos (R$ 200/mês), influencers locais

#### **Q4 (Mês 10-12): Consolidação**
- **Meta Apoiadores:** 80-100
- **Receita:** R$ 3.200-4.000/mês
- **Foco:** Parcerias corporativas, eventos maiores
- **Marketing:** SEO, blog, depoimentos em vídeo

---

### 5.2 Estratégias de Marketing (Baixo Custo)

#### **Gratuitas**
- **Redes Sociais:** Posts 3x/semana (Instagram, Facebook)
- **WhatsApp Business:** Lista broadcast mensal
- **Email Marketing:** Newsletter quinzenal (Resend gratuito)
- **Google Meu Negócio:** Perfil completo
- **Parcerias Locais:** Igrejas, escolas, comércio

#### **Investimento Baixo (R$ 200-500/mês)**
- **Facebook/Instagram Ads:** R$ 10/dia
- **Google Ads:** R$ 5/dia (palavras locais)
- - **Panfletos/Cartões:** R$ 100 (1000 unidades)
- **Eventos presenciais:** R$ 100-200/evento

---

### 5.3 Fontes de Receita Complementares

#### **1. Doações Únicas (Sem Recorrência)**
```
Botão no site: "Doe agora"
Valores sugeridos: R$ 20, R$ 50, R$ 100, Outro
```
**Potencial:** +R$ 500-1.000/mês

#### **2. Campanhas Temáticas**
```
- Dia das Crianças: Meta R$ 3.000 (presentes)
- Natal: Meta R$ 5.000 (cestas básicas)
- Volta às Aulas: Meta R$ 2.000 (material)
```
**Potencial:** +R$ 10.000/ano

#### **3. Parcerias Corporativas**
```
Empresa patrocinadora: R$ 500/mês
Benefícios: Logo no site, certificado, visita à ONG
```
**Potencial:** +R$ 1.500/mês (3 empresas)

#### **4. Eventos Beneficentes**
```
- Bingo online: R$ 5/cartela
- Rifa: R$ 10/número
- Live solidária: Doações espontâneas
```
**Potencial:** +R$ 2.000-5.000/evento

---

### 5.4 Indicadores de Sucesso (KPIs)

#### **Financeiros**
- CAC (Custo de Aquisição): < R$ 50/apoiador
- LTV (Valor Vitalício): > R$ 480 (12 meses × R$ 40)
- Churn (Cancelamento): < 5%/mês
- Margem Líquida: > 60%

#### **Operacionais**
- Taxa de conversão (visitante → apoiador): > 2%
- Taxa de abertura email: > 25%
- Tempo médio de apoio: > 8 meses
- NPS (Satisfação): > 50

#### **Técnicos**
- Uptime: > 99.5%
- Tempo de carregamento: < 3s
- Taxa de erro API: < 0.1%

---

## 🎯 PARTE 6: PERGUNTAS FREQUENTES

### 6.1 Técnicas

**P: Preciso saber programar para manter o site?**  
R: Não. Para operação diária (cadastrar crianças, gerar relatórios, ver doações), o painel é visual. Mas para bugs ou novas funcionalidades, precisarão de desenvolvedor.

**P: E se o site cair?**  
R: Com CloudWatch configurado, vocês recebem email/SMS automático. Podem ter um desenvolvedor de plantão (contrato avulso) ou contratar suporte gerenciado.

**P: Quanto custa suporte técnico?**  
R: Freelancer: R$ 50-100/hora. Mensalidade básica: R$ 300-500/mês (4-8h/mês). Emergências: R$ 150-200/chamado.

**P: O Free Tier AWS é realmente grátis?**  
R: Sim, mas apenas no 1º ano e com limites (750h/mês de EC2 e RDS). Depois, volta ao preço cheio. E se ultrapassarem os limites, cobram a diferença.

**P: Posso usar hospedagem mais barata?**  
R: Sim. Alternativas: Hostinger (R$ 30/mês), DigitalOcean (R$ 25/mês), ou até Hostgator compartilhado (R$ 15/mês). Mas AWS dá mais controle e escalabilidade.

---

### 6.2 Financeiras

**P: E se não conseguirmos 13 apoiadores?**  
R: Terão déficit nos primeiros meses (normal). Por isso o fundo de reserva de R$ 1.500-2.000 cobre essa fase até consolidar.

**P: As taxas do gateway são negociáveis?**  
R: Para ONGs, alguns gateways oferecem desconto. Mercado Pago: solicitem "conta para instituição social" (pode cair para 3.5%). PagSeguro tem programa específico para ONGs.

**P: Podemos aceitar doações em dinheiro?**  
R: Sim, mas precisam emitir recibo manual e registrar no sistema. Recomendamos focar em Pix (mais rastreável para transparência).

**P: Como declarar no Imposto de Renda?**  
R: Vocês (ONG) precisam de CNPJ ativo e emitir recibo anual aos doadores. Eles podem deduzir até 6% do IR devido (Lei de Incentivo). Consultar contador é essencial.

---

### 6.3 Operacionais

**P: Quanto tempo para o site ficar pronto?**  
R: Seguindo o cronograma: 6-8 semanas do início ao lançamento público.

**P: Precisamos de CNPJ?**  
R: Sim, obrigatório para:
- Conta no gateway de pagamento
- Emissão de recibos fiscais
- Contratos com AWS e outros serviços

**P: E se uma criança sair do programa?**  
R: O sistema permite marcar como "inativa" e redirecionar o apadrinhamento para outra criança. Comunicar ao apoiador com transparência.

**P: Podemos mudar o valor da mensalidade depois?**  
R: Sim, mas deve comunicar com 30 dias de antecedência e dar opção de cancelamento sem multa (boa prática).

---

## 📋 PARTE 7: PRÓXIMOS PASSOS IMEDIATOS

### Decisões que a Equipe Precisa Tomar AGORA

#### **1. Financeira**
- [ ] Aprovar mensalidade de **R$ 40/mês**?
- [ ] Aprovar fundo de reserva de **R$ 2.000**?
- [ ] Definir responsável pelas finanças

#### **2. Técnica**
- [ ] Contratar desenvolvedor para finalizar integrações? (Custo: R$ 1.500-3.000)
- [ ] Ou vocês mesmos tentam seguir o roteiro? (Tempo: 8 semanas)
- [ ] Definir responsável técnico

#### **3. Operacional**
- [ ] Registrar domínio (qual nome? Sugestões: `ongsos.com.br`, `sostudepelosocial.com.br`)
- [ ] Criar conta AWS (quem terá acesso?)
- [ ] Criar conta Mercado Pago/PagSeguro
- [ ] Obter termos de autorização das crianças

#### **4. Marketing**
- [ ] Definir responsável pelas redes sociais
- [ ] Criar Instagram/Facebook da ONG
- [ ] Preparar lista inicial de contatos (meta: 50 pessoas)

---

### Cronograma Sugerido (Próximos 7 Dias)

#### **Dia 1 (Hoje):**
- Ler este documento completo
- Reunião de equipe (2h)
- Tomar as 4 decisões acima

#### **Dia 2-3:**
- Criar contas (AWS, Resend, Gateway)
- Registrar domínio
- Solicitar documentação jurídica (CNPJ, termos)

#### **Dia 4-5:**
- Contratar desenvolvedor OU começar implementação
- Preparar conteúdo de marketing

#### **Dia 6-7:**
- Revisar código com desenvolvedor
- Testar integrações localmente
- Preparar lista de primeiros apoiadores

---

## 📞 CONTATOS E RECURSOS

### Suporte Técnico Recomendado
- **AWS Support:** Basic (gratuito) ou Developer ($29/mês)
- **Comunidades:** Fórum Laravel Brasil, Stack Overflow em português
- **Consultoria:** Freelancer no Workana (R$ 50-100/h)

### Ferramentas de Gestão
- **Trello/Notion:** Gerenciamento de tarefas (gratuito)
- **Google Analytics:** Monitorar tráfego do site (gratuito)
- **Hotjar:** Mapas de calor, gravações de sessão (gratuito até 35 sessões/dia)

### Documentação Oficial
- **Laravel:** https://laravel.com/docs
- **AWS:** https://docs.aws.amazon.com
- **Mercado Pago:** https://www.mercadopago.com.br/developers
- **Resend:** https://resend.com/docs

---

## ✅ CONCLUSÃO E RECOMENDAÇÕES FINAIS

### O Que Vocês TÊM de Bom
✅ Backend robusto e testado (243 testes aprovados)  
✅ Frontend funcional e bonito  
✅ Equipe comprometida  
✅ Documentação técnica completa  
✅ Segurança implementada (senha forte, LGPD)

### O Que Vocês PRECISAM Fazer
⚠️ Conectar frontend ao backend (prioridade #1)  
⚠️ Implementar gateway de pagamento  
⚠️ Configurar emails transacionais  
⚠️ Provisionar infraestrutura AWS  

### Nossa Recomendação
**CONTRATAR** um desenvolvedor experiente para finalizar as 4 pendências acima (2-3 semanas, R$ 2.000-3.000) e vocês focam em:
- Conseguir os primeiros 25 apoiadores
- Documentação jurídica (CNPJ, termos)
- Marketing e divulgação

**Investimento Total Inicial:**
- Desenvolvimento: R$ 2.500
- Fundo de reserva (3 meses AWS): R$ 1.500
- Domínio + contas: R$ 100
- **Total: R$ 4.100**

**Retorno Esperado (6 meses):**
- 30 apoiadores × R$ 40 × 6 meses = R$ 7.200
- **ROI: +75% no primeiro semestre**

---

### Mensagem Final

Vocês têm um projeto **tecnicamente sólido** e uma causa **socialmente relevante**. O maior risco não é técnico (o código está bom), mas sim:

1. **Operacional:** Conseguir apoiadores consistentemente
2. **Financeiro:** Ter reserva para os primeiros 3 meses de déficit
3. **Jurídico:** Documentação em dia (CNPJ, termos, autorizações)

Nossa sugestão: **invistam R$ 4.000 agora** (desenvolvimento + reserva) para ter uma plataforma 100% funcional, e foquem energia em conseguir apoiadores. Com 25 pessoas (meta realista em 3 meses), vocês atingem sustentabilidade.

**A AWS é uma escolha acertada** — vocês ganham experiência valiosa, têm controle total, e o custo é previsível. Depois de consolidados (100+ apoiadores), podem avaliar Laravel Cloud ou outras opções gerenciadas.

---

**Dúvidas? Próximo passo?**

Posso ajudar a:
1. Implementar as integrações pendentes (frontend ↔ backend, pagamento, email)
2. Criar scripts de deploy automatizados
3. Configurar a infraestrutura AWS passo a passo
4. Revisar contratos com fornecedores

**Qual área vocês querem priorizar agora?**

---

*Documento gerado em: 26/10/2026*  
*Projeto: InterADS4M — ONG SOS Tudo pelo Social*  
*Versão: 1.0*
