@php $pct = (int)($stats['pct'] ?? 0); @endphp

<div class="stats">
    <div class="chips">
        <span class="chip">Ukupno: <b>{{ $stats['total'] }}</b></span>
        <span class="chip">Aktivno: <b>{{ $stats['active'] }}</b></span>
        <span class="chip">Završeno: <b>{{ $stats['done'] }}</b></span>
        <span class="chip">Progress: <b>{{ $pct }}%</b></span>
    </div>

    <div class="bar" title="Progress">
        <span style="width: {{ $pct }}%"></span>
    </div>
</div>
