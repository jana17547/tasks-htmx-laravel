@if(!empty($toast))
    <div class="toast {{ $toast['type'] ?? 'success' }}">
        <div>{{ $toast['message'] ?? '' }}</div>

        @if(!empty($toast['undo']))
            <button class="btn"
                    hx-post="{{ $toast['undo']['url'] }}"
                    hx-target="#task-list"
                    hx-swap="afterbegin settle:120ms"
                    hx-include="#q,#status">
                {{ $toast['undo']['label'] ?? 'Vrati' }}
            </button>
        @else
            <button class="btn ghost" onclick="document.getElementById('toast').innerHTML=''">OK</button>
        @endif
    </div>
@endif
