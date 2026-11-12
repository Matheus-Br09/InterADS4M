// O servidor informa quais páginas de conteúdo (notícias, programas, materiais, transparência) já têm algo publicado.
// Sem essa informação (ou com a seção vazia), o link fica escondido para ninguém cair numa página vazia.
export function secaoDisponivel(config, chave) {
  return config?.secoes?.[chave] === true
}
