@php
    $prio = $task->priority;
    $prioClass = $prio === 'high' ? 'badge b-high' : ($prio === 'low' ? 'badge b-low' : 'badge');
    $isOverdue = $task->due_date && !$task->done && $task->due_date->isPast();
@endphp

<li class="task {{ $task->done ? 'done' : '' }}" id="task-{{ $task->id }}">
    <div class="left">
        <button class="iconbtn"
                hx-patch="{{ route('tasks.toggle', $task) }}"
                hx-target="#task-{{ $task->id }}"
                hx-swap="morphdom settle:80ms"
                hx-include="#q,#status"
                title="Toggle">
            {{ $task->done ? '↩️' : '✅' }}
        </button>

        <div style="min-width:0">
            <div class="title">{{ $task->title }}</div>

            <div class="meta">
                <span class="{{ $prioClass }}">{{ strtoupper($prio) }}</span>

                @if($task->due_date)
                    <span class="badge {{ $isOverdue ? 'b-over' : 'b-due' }}">
                        &#64;{{ $task->due_date->format('Y-m-d') }}{{ $isOverdue ? ' (kasni)' : '' }}
                    </span>
                @endif

                @if($task->done)
                    <span class="badge">DONE</span>
                @endif
            </div>
        </div>
    </div>

    <div style="display:flex;gap:8px;">
        <button class="iconbtn"
                hx-get="{{ route('tasks.edit', $task) }}"
                hx-target="#task-{{ $task->id }}"
                hx-swap="morphdom settle:80ms"
                title="Edit">✏️</button>

        <button class="iconbtn"
                hx-delete="{{ route('tasks.destroy', $task) }}"
                hx-target="#task-{{ $task->id }}"
                hx-swap="delete swap:180ms"
                hx-include="#q,#status"
                hx-confirm="Obrisati task?"
                title="Delete">🗑️</button>
    </div>
</li>
