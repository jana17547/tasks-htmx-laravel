@if($bag->any())
    <div class="alert">
        <b>Proveri unos:</b>
        <ul style="margin:8px 0 0 18px;">
            @foreach($bag->all() as $msg)
                <li>{{ $msg }}</li>
            @endforeach
        </ul>
    </div>
@endif
