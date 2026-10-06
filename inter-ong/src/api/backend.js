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
  cadastrar: (dados) => gravar('/auth/cadastro', 'POST', dados),
  entrar: (dados) => gravar('/auth/entrar', 'POST', dados),
  sair: () => gravar('/auth/sair', 'POST'),
  conta: (options) => get('/auth/eu', options),
  atualizarConta: (dados) => gravar('/conta', 'PATCH', dados),
  trocarSenha: (dados) => gravar('/auth/senha', 'PUT', dados),
  doacao: (dados) => gravar('/doacoes', 'POST', dados),
  mensalidade: (dados) => gravar('/mensalidades', 'POST', dados),
  apadrinhamento: (dados) => gravar('/apadrinhamentos', 'POST', dados),
  historico: (tipo, options) => get(`/conta/${tipo}`, options),
  cancelarApoio: (tipo, id) => gravar(`/conta/${tipo}/${id}`, 'DELETE'),
  criancas: (options) => request('/api/criancas', options),
  voluntariado: (options) => get('/voluntariado', options),
  inscreverVoluntario: (dados) => gravar('/voluntariado', 'POST', dados),
  conteudos: (tipo, pagina = 1, options) => get(`/conteudos/${tipo}?page=${pagina}`, options),
  estatisticas: (options) => get('/estatisticas', options),
  newsletter: (dados) => gravar('/newsletter', 'POST', dados),
  publicarMaterial: (dados) => gravar('/gestao/conteudos/materiais', 'POST', dados),
  publicarNoticia: (dados) => gravar('/gestao/conteudos/noticias', 'POST', dados),
  excluirNoticia: (id) => gravar(`/gestao/conteudos/noticias/${id}`, 'DELETE'),
  arquivoConteudo: (tipo, id, campo = 'arquivo_pdf') => `${API_BASE}/api/v1/conteudos/${tipo}/${id}/arquivos/${campo}`,
  imagemCrianca: (arquivo) => `${API_BASE}/img/${encodeURIComponent((arquivo || '').split('/').pop())}`,
}
