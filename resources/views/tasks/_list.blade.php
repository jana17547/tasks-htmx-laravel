@forelse($tasks as $task)
    @include('tasks._item', ['task' => $task])
@empty
    <li class="task" style="justify-content:center;">
        <span class="hint">Nema taskova za ovaj filter/search.</span>
    </li>
@endforelse




