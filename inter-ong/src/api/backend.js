const API_BASE = import.meta.env.VITE_BACKEND_URL || ''
let csrfToken = ''

async function request(path, { headers: customHeaders = {}, ...options } = {}) {
  const response = await fetch(`${API_BASE}/api/v1${path}`, {
    credentials: 'include',
    headers: {
      Accept: 'application/json',
      ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
      ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
      ...customHeaders,
    },
    ...options,
  })

  if (!response.ok) {
    const error = await response.json().catch(() => ({}))
    const failure = new Error(error.message || 'Não foi possível concluir a operação.')
    failure.status = response.status
    failure.errors = error.errors || {}
    throw failure
  }

  return response.status === 204 ? null : response.json()
}

export async function prepararCsrf() {
  const resposta = await request('/csrf')
  csrfToken = resposta.token
  return resposta
}

export const backend = {
  cadastrar: (dados) => request('/auth/cadastro', { method: 'POST', body: JSON.stringify(dados) }),
  entrar: (dados) => request('/auth/entrar', { method: 'POST', body: JSON.stringify(dados) }),
  sair: () => request('/auth/sair', { method: 'POST' }),
  conta: () => request('/auth/eu'),
  atualizarConta: (dados) => request('/conta', { method: 'PATCH', body: JSON.stringify(dados) }),
  trocarSenha: (dados) => request('/auth/senha', { method: 'PUT', body: JSON.stringify(dados) }),
  doacao: (dados) => request('/doacoes', { method: 'POST', body: JSON.stringify(dados) }),
  mensalidade: (dados) => request('/mensalidades', { method: 'POST', body: JSON.stringify(dados) }),
  apadrinhamento: (dados) => request('/apadrinhamentos', { method: 'POST', body: JSON.stringify(dados) }),
  voluntariado: () => request('/voluntariado'),
  inscreverVoluntario: (formulario) => request('/voluntariado', { method: 'POST', body: formulario }),
  conteudos: (tipo, pagina = 1) => request(`/conteudos/${tipo}?page=${pagina}`),
  estatisticas: () => request('/estatisticas'),
  newsletter: (dados) => request('/newsletter', { method: 'POST', body: JSON.stringify(dados) }),
}
