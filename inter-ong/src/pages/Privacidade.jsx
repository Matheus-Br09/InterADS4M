const H2 = 'text-xl font-bold text-slate-900 dark:text-white'

export default function Privacidade() {
  return (
    <main className="mx-auto max-w-3xl px-4 py-12 sm:px-6 text-slate-700 dark:text-slate-300">
      <h1 className="text-3xl font-extrabold text-slate-900 dark:text-white sm:text-4xl">Política de privacidade</h1>
      <p className="mt-3">
        Esta página explica, de forma simples, como a SOS Tudo pelo Social cuida das informações que você nos entrega pelo site.
      </p>

      {/* REVISAR COM A ONG */}
      <section className="mt-8 space-y-6">
        <div>
          <h2 className={H2}>Quais dados coletamos</h2>
          <ul className="mt-2 list-disc space-y-1 pl-6">
            <li>Voluntários: nome completo, e-mail, celular, data de nascimento, área de atuação, disponibilidade e, se você quiser enviar, o currículo em PDF.</li>
            <li>Doadores: os dados que você informar ao avisar sobre uma doação por Pix. Não guardamos senha de banco nem dados de cartão.</li>
            <li>Newsletter: seu e-mail e, se quiser informar, seu nome.</li>
          </ul>
        </div>

        <div>
          <h2 className={H2}>Para que usamos</h2>
          <p className="mt-2">
            Usamos os dados só para falar com você sobre o que você pediu: avaliar sua inscrição como voluntário, registrar e agradecer uma doação ou enviar as novidades da ONG. Não vendemos nem repassamos seus dados.
          </p>
        </div>

        <div>
          <h2 className={H2}>Quem pode ver</h2>
          <p className="mt-2">Somente a equipe de gestão da ONG, com login próprio, tem acesso a essas informações.</p>
        </div>

        <div>
          <h2 className={H2}>Por quanto tempo guardamos</h2>
          <p className="mt-2">
            Guardamos os dados pelo tempo necessário para as finalidades acima. Quando não forem mais necessários, ou se você pedir, eles serão apagados ou deixados sem identificação.
          </p>
        </div>

        <div>
          <h2 className={H2}>Como pedir a exclusão dos seus dados</h2>
          <p className="mt-2">
            Escreva para <a className="underline" href="mailto:sostudopelosocial@gmail.com">sostudopelosocial@gmail.com</a> dizendo seu nome e e-mail. Você também pode pedir para ver ou corrigir os dados que temos sobre você.
          </p>
        </div>

        <div>
          <h2 className={H2}>Lei Geral de Proteção de Dados (LGPD)</h2>
          <p className="mt-2">
            Seguimos a LGPD (Lei nº 13.709/2018), a lei brasileira que protege os dados pessoais. Para ser voluntário é preciso ter 18 anos ou mais.
          </p>
        </div>

        <div>
          <h2 className={H2}>Imagens de crianças</h2>
          <p className="mt-2">
            Fotos e vídeos de crianças e adolescentes só são publicados com a autorização de quem é responsável por eles. Se você quiser que uma imagem seja retirada do site, fale conosco no e-mail acima.
          </p>
        </div>
      </section>
    </main>
  )
}
