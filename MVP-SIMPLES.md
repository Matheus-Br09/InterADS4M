# 🎯 MVP Simplificado — Site ONG SOS (Essencial para Ir ao Ar)

**Projeto:** InterADS4M  
**Objetivo:** Site no ar com doações e voluntariado funcionando  
**Prazo:** 2-3 semanas  
**Data:** 26/10/2026

---

## ✅ O QUE O SITE PRECISA TER (MÍNIMO)

### **Para o Visitante:**
1. ✅ Ver informações da ONG (landing page)
2. ✅ **Fazer doação única via Pix**
3. ✅ **Cadastrar-se como voluntário**
4. ✅ Ver notícias/transparência

### **Para a Gestão:**
1. ✅ Ver lista de doações recebidas
2. ✅ Ver lista de voluntários inscritos
3. ✅ Aprovar/rejeitar voluntários

---

## ❌ O QUE FICA PARA DEPOIS (Versão 2.0)

- ❌ Sistema de login de apoiadores
- ❌ Área logada do apoiador
- ❌ Mensalidade recorrente automática
- ❌ Apadrinhamento de criança
- ❌ Emails automáticos (fazer manual por enquanto)
- ❌ Pagamento com cartão (só Pix por ora)

---

## 🚀 STATUS ATUAL DO CÓDIGO

### ✅ **JÁ FUNCIONA (Backend)**
- API de doação: `POST /api/v1/doacoes`
- API de voluntariado: `POST /api/v1/voluntariado`
- Painel admin para ver dados
- Banco de dados estruturado
- 243 testes passando

### ✅ **JÁ FUNCIONA (Frontend)**
- Landing page completa
- Página de doação (`/doar`)
- Página de voluntariado (`/voluntariado`)
- Modal Pix com chave copiável
- Design responsivo (mobile + desktop)

### ⚠️ **PROBLEMA ATUAL**
O frontend está tentando autenticar o usuário para fazer doação/voluntariado, mas **não precisa disso** para o MVP simples.

**Solução:** Fazer doação e voluntariado **anônimos** (sem exigir login).

---

## 🔧 O QUE PRECISA AJUSTAR (3 Tarefas Simples)

### **TAREFA 1: Remover Autenticação das Páginas Públicas**

**Arquivo:** `inter-ong/src/pages/Voluntariado.jsx`

**Problema atual:** Linha 17 tenta buscar inscrição do usuário logado
```javascript
backend.voluntariado({ signal: controller.signal }) // Precisa estar logado
```

**Solução:** Remover essa checagem, permitir qualquer pessoa se cadastrar

---

**Arquivo:** `inter-ong/src/pages/Doar.jsx`

**Está OK!** Já permite doação anônima (linha 49):
```javascript
await backend.doacao({ valor, metodo_pagamento: 'pix' })
```

---

### **TAREFA 2: Simplificar Formulário de Voluntário**

**Problema:** O formulário pede currículo em PDF e usa dados da conta logada.

**Solução:** Adicionar campos básicos no formulário:
- Nome completo
- Email
- Telefone
- Área de atuação
- Disponibilidade
- Currículo PDF (opcional)

---

### **TAREFA 3: Backend — Aceitar Voluntários Anônimos**

**Arquivo:** `backend/app/Http/Controllers/VoluntarioApiController.php`

**Ajuste:** A rota `POST /api/v1/voluntariado` precisa aceitar dados sem autenticação.

**Campos novos no banco `voluntarios`:**
- `nome` (já existe)
- `email` (já existe)
- `celular` (já existe)
- Remove a obrigatoriedade de `apoiador_id` (pode ser NULL)

---

## 💰 CUSTOS SIMPLIFICADOS

### **Opção 1: Hostinger (Mais Barato)**
```
Plano Premium: R$ 29,99/mês
- Hospedagem compartilhada
- MySQL incluído
- SSL grátis
- 100GB armazenamento
- Fácil de configurar
```

**Ideal para:** Começar rápido, até 1.000 visitantes/dia

---

### **Opção 2: AWS (Aprendizado + Escalável)**
```
Custo Mês 1-12 (Free Tier): R$ 150-200/mês
- EC2 t3.micro
- RDS MySQL t3.micro
- S3 + CloudFront
- Mais complexo de configurar
```

**Ideal para:** Ganhar experiência, crescimento futuro

---

### **Nossa Recomendação para MVP:**
**COMEÇAR COM HOSTINGER** (R$ 30/mês) e depois migrar para AWS quando tiverem mais apoiadores.

---

## 💵 ESTRATÉGIA DE DOAÇÃO SIMPLIFICADA

### **Modelo Único: Doação Pix**

```
💎 DOAÇÃO ÚNICA VIA PIX

Como funciona:
1. Visitante escolhe valor (R$ 25, 50, 100, outro)
2. Site gera protocolo e mostra chave Pix
3. Visitante copia chave, faz Pix no banco
4. Gestão recebe notificação e confirma manualmente
5. Status da doação atualizado no painel
```

**Por enquanto SEM:**
- ❌ Gateway de pagamento (taxa de 5%)
- ❌ Confirmação automática (webhook)
- ❌ Pagamento com cartão
- ❌ Mensalidade recorrente

**Validação manual:** A gestão vê no extrato bancário e marca como "concluída" no painel.

---

## 📋 CHECKLIST DE DEPLOY SIMPLIFICADO

### **Pré-requisitos:**
- [ ] Domínio registrado (ex: `ongsos.com.br`) — R$ 40/ano
- [ ] Conta Hostinger ou AWS
- [ ] Chave Pix da ONG (CNPJ)

---

### **Semana 1: Ajustes de Código**

#### **Backend:**
- [ ] Tornar `apoiador_id` nullable na tabela `voluntarios`
- [ ] Remover middleware `auth:apoiador` da rota `POST /api/v1/voluntariado`
- [ ] Adicionar validação de email/telefone no controller

#### **Frontend:**
- [ ] Adicionar campos nome/email/telefone no formulário de voluntário
- [ ] Remover tentativa de buscar inscrição (linha 17 Voluntariado.jsx)
- [ ] Testar fluxo completo: doação + voluntariado

#### **Testes:**
- [ ] `cd backend && php artisan test` (deve passar)
- [ ] `cd inter-ong && npm test` (deve passar)
- [ ] Testar manualmente no navegador

---

### **Semana 2: Deploy Hostinger**

#### **Passo 1: Upload Backend**
1. Compactar pasta `backend/` em ZIP
2. No painel Hostinger: File Manager
3. Upload e descompactar em `public_html/api/`
4. Criar banco MySQL pelo painel
5. Editar `.env` com credenciais do banco
6. Executar: `php artisan migrate --force`

#### **Passo 2: Upload Frontend**
1. No computador: `cd inter-ong && npm run build`
2. Pasta gerada: `inter-ong/dist/`
3. Upload da pasta `dist/` para `public_html/` no Hostinger
4. Configurar domínio para apontar para `public_html/`

#### **Passo 3: DNS**
1. Apontar domínio para Hostinger (nameservers)
2. Aguardar propagação (até 48h)
3. Ativar SSL grátis no painel Hostinger

---

### **Semana 3: Testes e Ajustes**

- [ ] Testar doação Pix de ponta a ponta
- [ ] Testar cadastro de voluntário
- [ ] Verificar se emails da gestão estão recebendo notificações
- [ ] Testar no celular (responsive)
- [ ] Corrigir bugs encontrados
- [ ] Lançamento soft (10 amigos)
- [ ] Ajustar com base no feedback
- [ ] **LANÇAMENTO PÚBLICO** 🎉

---

## 📊 PROJEÇÃO FINANCEIRA SIMPLES

### **Custos Mensais (Hostinger):**
```
Hospedagem: R$ 30/mês
Domínio: R$ 3/mês (R$ 40/ano ÷ 12)
TOTAL: R$ 33/mês
```

### **Receita Mínima Necessária:**
```
Para cobrir custos: 1 doação de R$ 50/mês
Para ser sustentável: 5 doações de R$ 50 = R$ 250/mês
```

### **Meta Realista (6 meses):**
```
Doações mensais: R$ 1.000-2.000
Voluntários cadastrados: 10-20 pessoas
Custos: R$ 33/mês
Sobra para ONG: R$ 970-1.970/mês
```

---

## 🎯 FLUXO DO USUÁRIO (Como Vai Funcionar)

### **CENÁRIO 1: Visitante quer doar**

```
1. Entra no site → Clica "Doar"
2. Escolhe valor: R$ 50
3. Clica "Doar via Pix"
4. Aparece modal com:
   - Chave Pix: 48.219.821/0001-90
   - Botão "Copiar Chave"
   - Protocolo: #123456
5. Abre app do banco, cola chave, faz Pix
6. Site mostra: "Doação registrada! Aguardando confirmação"
7. Gestão recebe notificação por email
8. Gestão vê no extrato bancário
9. Gestão marca como "concluída" no painel
```

**Simples, transparente, sem taxa de gateway!**

---

### **CENÁRIO 2: Visitante quer ser voluntário**

```
1. Entra no site → Clica "Voluntariado"
2. Preenche formulário:
   - Nome: João Silva
   - Email: joao@email.com
   - Telefone: (11) 98765-4321
   - Área: Nutrição
   - Disponibilidade: Manhã
   - Currículo PDF (opcional)
3. Clica "Enviar inscrição"
4. Site mostra: "Inscrição enviada! Entraremos em contato"
5. Gestão recebe notificação por email
6. Gestão vê cadastro no painel
7. Gestão aprova/rejeita e liga para o voluntário
```

**Sem complicação de login/senha!**

---

## ⚠️ IMPORTANTE: O que NÃO fazer

### **Não tentem implementar tudo de uma vez:**
- ❌ Sistema de login complexo
- ❌ Recuperação de senha por email
- ❌ Gateway de pagamento automático
- ❌ Mensalidade recorrente
- ❌ Painel do apoiador

### **Foquem APENAS em:**
- ✅ Site no ar
- ✅ Doação Pix funcionando
- ✅ Voluntário se cadastrando
- ✅ Gestão vendo os dados

**Depois que isso estiver rodando 100%, vocês adicionam as outras funcionalidades!**

---

## 🚀 PRÓXIMOS PASSOS (Hoje)

### **Decisões Imediatas:**

1. **Hospedagem:** Hostinger (R$ 30/mês) ou AWS (R$ 200/mês)?
   - Recomendação: **Hostinger** para começar

2. **Domínio:** Qual nome? Sugestões:
   - `ongsos.com.br`
   - `sostudepelosocial.com.br`
   - `institutosos.com.br`

3. **Desenvolvimento:** Vocês mesmos ou contratar?
   - Se contratarem: R$ 800-1.500 (2-3 semanas)
   - Se fizerem: Posso ajudar com os ajustes de código agora

---

## ✅ RESUMO EXECUTIVO

### **O que vocês TÊM:**
- ✅ Código 90% pronto
- ✅ Design bonito e responsivo
- ✅ Backend testado (243 testes)

### **O que vocês PRECISAM:**
- ⚠️ 3 ajustes simples no código (2-3 horas de trabalho)
- ⚠️ Escolher hospedagem e domínio
- ⚠️ Fazer deploy (1 semana)

### **Quanto custa:**
- 💰 Hostinger: R$ 30/mês
- 💰 Domínio: R$ 40/ano
- 💰 **Total: R$ 33/mês**

### **Quanto tempo:**
- ⏱️ Ajustes código: 1 semana
- ⏱️ Deploy: 1 semana
- ⏱️ Testes: 1 semana
- ⏱️ **Total: 3 semanas para o site no ar**

---

## 🤝 COMO POSSO AJUDAR AGORA

Posso fazer os 3 ajustes de código **hoje mesmo**:

1. ✅ Tornar voluntariado anônimo (sem login)
2. ✅ Adicionar campos nome/email/telefone no formulário
3. ✅ Ajustar backend para aceitar cadastros sem autenticação

**Quer que eu faça isso agora?** Leva 1-2 horas e o site fica pronto para deploy.

Depois vocês só precisam:
- Registrar domínio
- Contratar Hostinger
- Fazer upload dos arquivos

**E pronto! Site no ar recebendo doações e voluntários! 🎉**

---

**Decisão:** Posso começar os ajustes de código agora?
