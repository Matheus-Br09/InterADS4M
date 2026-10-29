<p>Uma nova inscrição de voluntário chegou pelo site.</p>
<ul>
    <li>Nome: {{ $voluntario->nome_completo }}</li>
    <li>Área de atuação: {{ $voluntario->area_atuacao }}</li>
    <li>Disponibilidade: {{ $voluntario->disponibilidade }}</li>
    <li>Currículo: {{ $voluntario->arquivo_curriculo ? 'enviado (veja no painel)' : 'não enviado' }}</li>
</ul>
<p>Veja os detalhes no painel.</p>
