const API_BASE = (import.meta.env?.VITE_BACKEND_URL || '').replace(/\/$/, '')

async function request(path, { headers = {}, ...options } = {}) {
  let response
  try {
    response = await fetch(`${API_BASE}${path}`, {
      ...options,
      credentials: 'include',
      headers: { Accept: 'application/json', ...(options.body && !(options.body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}), ...headers },
    })
  } catch (error) {
    if (error.name === 'AbortError') throw error
    throw new Error('Não foi possível conectar ao servidor. Tente novamente.', { cause: error })
  }
  if (response.status === 204) return null
  const text = await response.text()
  let data
  try { data = JSON.parse(text) } catch { data = null }
  if (!response.ok || !data) {
    const message = response.status === 413
      ? 'O arquivo excede o limite de envio do servidor. Escolha um PDF menor.'
      : text.includes('unable to create a temporary file')
        ? 'O servidor não conseguiu criar o arquivo temporário para o envio. Contate a equipe da ONG.'
        : data?.message || data?.mensagem || (!data ? 'Confira a configuração da API: o servidor não retornou JSON.' : 'Não foi possível concluir a operação.')
    throw Object.assign(new Error(message), { status: response.status, errors: data?.errors || {}, codigo: data?.codigo, tentarEm: data?.tentar_em })
  }
  return data
}

const get = (path, options) => request(`/api/v1${path}`, options)
export const prepararCsrf = () => get('/csrf')

async function gravar(path, method, dados) {
  // Busque um token por operação: login, logout e troca de senha regeneram a sessão.
  for (let tentativa = 0; tentativa < 2; tentativa++) {
    const { token } = await prepararCsrf()
    try {
      return await get(path, {
        method,
        headers: { 'X-CSRF-TOKEN': token },
        ...(dados === undefined ? {} : { body: dados instanceof FormData ? dados : JSON.stringify(dados) }),
      })
    } catch (error) {
      // Somente 419 é seguro para repetir: a gravação foi recusada pelo CSRF.
      if (error.status !== 419 || tentativa === 1) throw error
    }
  }
}

export const backend = {
  entrar: (dados) => gravar('/auth/entrar', 'POST', dados),
  sair: () => gravar('/auth/sair', 'POST'),
  conta: (options) => get('/auth/eu', options),
  trocarSenha: (dados) => gravar('/auth/senha', 'PUT', dados),
  doacao: (dados) => gravar('/doacoes', 'POST', dados),
  inscreverVoluntario: (dados) => gravar('/voluntariado', 'POST', dados),
  conteudos: (tipo, pagina = 1, options) => get(`/conteudos/${tipo}?page=${pagina}`, options),
  newsletter: (dados) => gravar('/newsletter', 'POST', dados),
  configPublica: (options) => get('/config-publica', options),
  gestaoDoacoes: (status, pagina = 1, options) => get(`/gestao/doacoes?${status ? `status=${encodeURIComponent(status)}&` : ''}page=${pagina}`, options),
  atualizarDoacao: (id, status) => gravar(`/gestao/doacoes/${id}`, 'PATCH', { status }),
  anonimizarDoacao: (id) => gravar(`/gestao/doacoes/${id}`, 'DELETE'),
  gestaoVoluntarios: (pagina = 1, options) => get(`/gestao/voluntarios?page=${pagina}`, options),
  atualizarVoluntario: (id, dados) => gravar(`/gestao/voluntarios/${id}`, 'PATCH', dados),
  excluirVoluntario: (id) => gravar(`/gestao/voluntarios/${id}`, 'DELETE'),
  curriculoUrl: (id) => `${API_BASE}/api/v1/gestao/voluntarios/${id}/curriculo`,
  gestaoConteudos: (tipo, pagina = 1, options) => get(`/gestao/conteudos/${tipo}?page=${pagina}`, options),
  publicarConteudo: (tipo, dados) => gravar(`/gestao/conteudos/${tipo}`, 'POST', dados),
  excluirConteudo: (tipo, id) => gravar(`/gestao/conteudos/${tipo}/${id}`, 'DELETE'),
  gestaoGestores: (options) => get('/gestao/gestores', options),
  criarGestor: (dados) => gravar('/gestao/gestores', 'POST', dados),
  desativarGestor: (id) => gravar(`/gestao/gestores/${id}`, 'DELETE'),
  arquivoConteudo: (tipo, id, campo = 'arquivo_pdf') => `${API_BASE}/api/v1/conteudos/${tipo}/${id}/arquivos/${campo}`,
}
