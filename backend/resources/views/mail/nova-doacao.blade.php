<p>Uma nova doação foi registrada no site.</p>
<ul>
    <li>Valor: R$ {{ number_format((float) $doacao->valor, 2, ',', '.') }}</li>
    <li>Protocolo: {{ $doacao->protocolo }}</li>
    <li>Data: {{ $doacao->data_doacao?->format('d/m/Y H:i') }}</li>
    @if ($doacao->nome_doador)
        <li>Nome: {{ $doacao->nome_doador }}</li>
    @endif
</ul>
<p>Confira no extrato e confirme no painel.</p>
