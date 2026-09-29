const API_BASE = (import.meta.env?.VITE_BACKEND_URL || '').replace(/\/+$/, '')

async function request(path, { headers: customHeaders = {}, ...options } = {}, retry = true) {
  const mutacao = !['GET', 'HEAD'].includes(options.method || 'GET')
  // Login, logout e troca de senha renovam o token da sessão no servidor.
  const csrf = mutacao ? await prepararCsrf() : null
  let response
  try {
    response = await fetch(`${API_BASE}/api/v1${path}`, {
      credentials: 'include',
      headers: {
        Accept: 'application/json',
        ...(csrf ? { 'X-CSRF-TOKEN': csrf.token } : {}),
        ...(options.body && !(options.body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}),
        ...customHeaders,
      },
      ...options,
    })
  } catch (error) {
    if (error.name === 'AbortError') throw error
    throw new Error('Não foi possível conectar ao servidor. Verifique sua conexão e tente novamente.', { cause: error })
  }

  if (response.status === 419 && mutacao && retry) {
    return request(path, { ...options, headers: customHeaders }, false)
  }

  if (!response.ok) {
    const textoErro = await response.text()
    let error = {}
    try {
      error = JSON.parse(textoErro)
    } catch {
      const jsonNoAviso = textoErro.match(/\{\s*"(?:message|mensagem)"[\s\S]*\}\s*$/)
      if (jsonNoAviso) {
        error = JSON.parse(jsonNoAviso[0])
      }
    }
    const falhaUploadTemporario = /File upload error|unable to create a temporary file/i.test(textoErro)
    const message = response.status === 419
      ? 'Sua sessão expirou. Atualize a página e entre novamente.'
      : response.status >= 500
        ? 'O servidor está indisponível. Tente novamente em instantes.'
        : falhaUploadTemporario
          ? 'O PHP não conseguiu criar o arquivo temporário do upload. Reinicie o servidor Laravel e verifique a pasta temporária do PHP.'
        : response.status === 413
          ? 'O arquivo excede o limite de envio do servidor. Escolha um PDF menor.'
          : error.message || error.mensagem || 'Não foi possível concluir a operação.'
    const failure = new Error(message)
    failure.status = response.status
    failure.errors = error.errors || {}
    failure.codigo = error.codigo
    failure.tentarEm = error.tentar_em
    throw failure
  }

  if (response.status === 204) return null
  if (!response.headers.get('content-type')?.includes('application/json')) {
    throw new Error('O servidor retornou uma resposta inesperada. Verifique a configuração da API.')
  }
  return response.json()
}

export async function prepararCsrf() {
  const resposta = await request('/csrf')
  if (!resposta.token) throw new Error('Não foi possível preparar uma sessão segura. Atualize a página.')
  return resposta
}

export const backend = {
  cadastrar: (dados) => request('/auth/cadastro', { method: 'POST', body: JSON.stringify(dados) }),
  entrar: (dados) => request('/auth/entrar', { method: 'POST', body: JSON.stringify(dados) }),
  sair: () => request('/auth/sair', { method: 'POST' }),
  conta: (options) => request('/auth/eu', options),
  atualizarConta: (dados) => request('/conta', { method: 'PATCH', body: JSON.stringify(dados) }),
  trocarSenha: (dados) => request('/auth/senha', { method: 'PUT', body: JSON.stringify(dados) }),
  doacao: (dados) => request('/doacoes', { method: 'POST', body: JSON.stringify(dados) }),
  mensalidade: (dados) => request('/mensalidades', { method: 'POST', body: JSON.stringify(dados) }),
  apadrinhamento: (dados) => request('/apadrinhamentos', { method: 'POST', body: JSON.stringify(dados) }),
  voluntariado: () => request('/voluntariado'),
  inscreverVoluntario: (formulario) => request('/voluntariado', { method: 'POST', body: formulario }),
  conteudos: (tipo, pagina = 1, options) => request(`/conteudos/${encodeURIComponent(tipo)}?page=${pagina}`, options),
  arquivoConteudo: (tipo, id, campo = 'arquivo_pdf') => `${API_BASE}/api/v1/conteudos/${encodeURIComponent(tipo)}/${encodeURIComponent(id)}/arquivos/${encodeURIComponent(campo)}`,
  publicarMaterial: (dados) => request('/gestao/conteudos/materiais', { method: 'POST', body: JSON.stringify(dados) }),
  estatisticas: () => request('/estatisticas'),
  newsletter: (dados) => request('/newsletter', { method: 'POST', body: JSON.stringify(dados) }),
}
