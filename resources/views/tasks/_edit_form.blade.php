<li class="task" id="task-{{ $task->id }}">
    <div style="width:100%;">
        @if(isset($bag) && $bag->any())
            <div class="alert">
                <b>Greška:</b>
                <ul style="margin:8px 0 0 18px;">
                    @foreach($bag->all() as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="row" style="margin-top:10px;"
              hx-put="{{ route('tasks.update', $task) }}"
              hx-target="#task-{{ $task->id }}"
              hx-swap="outerHTML"
              hx-include="#q,#status">
            <input type="text" name="title" value="{{ $task->title }}" maxlength="120" required>

            <select name="priority" style="max-width: 160px;">
                <option value="low" {{ $task->priority === 'low' ? 'selected' : '' }}>low</option>
                <option value="normal" {{ $task->priority === 'normal' ? 'selected' : '' }}>normal</option>
                <option value="high" {{ $task->priority === 'high' ? 'selected' : '' }}>high</option>
            </select>

            <input type="date" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}" style="max-width: 170px;">

            <button type="submit">Save</button>

            <button type="button"
                    hx-get="{{ route('tasks.row', $task) }}"
                    hx-target="#task-{{ $task->id }}"
                    hx-swap="outerHTML"
                    hx-include="#q,#status">Cancel</button>
        </form>
    </div>
</li>
