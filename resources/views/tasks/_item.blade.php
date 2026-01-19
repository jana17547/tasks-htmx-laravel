@php
    $prio = $task->priority;
    $prioClass = $prio === 'high'
        ? 'badge b-high'
        : ($prio === 'low' ? 'badge b-low' : 'badge');

    $isOverdue = $task->due_date && !$task->done && $task->due_date->isPast();
@endphp
<style>
    /* Checkbox button */
    .check-btn{
        padding:0;
    }

    /* Base checkbox */
    .check{
        width:22px;
        height:22px;
        border-radius:6px;
        border:1.5px solid rgba(255,255,255,.25);
        background: rgba(255,255,255,.04);
        display:grid;
        place-items:center;
        transition: all .18s ease;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,.35);
    }

    /* Hover (unchecked) */
    .iconbtn:hover .check{
        border-color: rgba(110,124,255,.55);
        background: rgba(110,124,255,.10);
    }

    /* Checkmark */
    .check::after{
        content:'';
        width:10px;
        height:6px;
        border-left:2px solid transparent;
        border-bottom:2px solid transparent;
        transform: rotate(-45deg) scale(.9);
        opacity:0;
        transition:.18s ease;
    }

    /* Checked state */
    .checked .check{
        background: linear-gradient(180deg, #39d98a, #2fcf7e);
        border-color: #39d98a;
        box-shadow:
            0 0 0 2px rgba(57,217,138,.18),
            inset 0 0 0 1px rgba(0,0,0,.35);
    }

    /* Show checkmark */
    .checked .check::after{
        border-left-color:#062c1b;
        border-bottom-color:#062c1b;
        opacity:1;
        transform: rotate(-45deg) scale(1);
    }

    /* Done text */
    .done .title{
        opacity:.65;
        text-decoration: line-through;
    }
</style>
<li class="task {{ $task->done ? 'done checked' : '' }}" id="task-{{ $task->id }}">
    <div class="left">
        {{-- CHECK / TOGGLE --}}
        <button class="iconbtn check-btn"
                hx-patch="{{ route('tasks.toggle', $task) }}"
                hx-target="#task-{{ $task->id }}"
                hx-swap="morphdom settle:80ms"
                hx-include="#q,#status"
                title="Mark as done">
            <span class="check"></span>
        </button>

        {{-- CONTENT --}}
        <div style="min-width:0">
            <div class="title">{{ $task->title }}</div>

            <div class="meta">
                <span class="{{ $prioClass }}">{{ strtoupper($prio) }}</span>

                @if($task->due_date)
                    <span class="badge {{ $isOverdue ? 'b-over' : 'b-due' }}">
                        &#64;{{ $task->due_date->format('Y-m-d') }}
                        {{ $isOverdue ? ' (kasni)' : '' }}
                    </span>
                @endif

                @if($task->done)
                    <span class="badge">DONE</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div style="display:flex;gap:8px;">
        <button class="iconbtn"
                hx-get="{{ route('tasks.edit', $task) }}"
                hx-target="#task-{{ $task->id }}"
                hx-swap="morphdom settle:80ms"
                title="Edit">
            ✏️
        </button>

        <button class="iconbtn"
                hx-delete="{{ route('tasks.destroy', $task) }}"
                hx-target="#task-{{ $task->id }}"
                hx-swap="delete swap:180ms"
                hx-include="#q,#status"
                hx-confirm="Obrisati task?"
                title="Delete">
            🗑️
        </button>
    </div>
</li>
