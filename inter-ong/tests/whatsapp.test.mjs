import assert from 'node:assert/strict'
import { test } from 'node:test'
import { linkWhatsapp } from '../src/utils/whatsapp.js'

test('linkWhatsapp monta o link só com os dígitos do número', () => {
  assert.equal(linkWhatsapp('5581987118820'), 'https://wa.me/5581987118820')
  assert.equal(linkWhatsapp('+55 (81) 98711-8820'), 'https://wa.me/5581987118820')
})

test('linkWhatsapp devolve null sem número configurado', () => {
  assert.equal(linkWhatsapp(''), null)
  assert.equal(linkWhatsapp(undefined), null)
})

test('linkWhatsapp inclui a mensagem pronta, codificada para URL', () => {
  const link = linkWhatsapp('5581987118820', 'Olá! Vim pelo site.')
  assert.equal(link, 'https://wa.me/5581987118820?text=Ol%C3%A1!%20Vim%20pelo%20site.')
})

test('linkWhatsapp ignora mensagem vazia', () => {
  assert.equal(linkWhatsapp('5581987118820', '  '), 'https://wa.me/5581987118820')
})
