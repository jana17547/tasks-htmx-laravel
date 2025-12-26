<div id="tasks-region">
    <div id="stats">
        @include('tasks._stats', ['stats' => $stats])
    </div>

    <div class="divider"></div>

    <ul id="task-list">
        @include('tasks._list', ['tasks' => $tasks])
    </ul>
</div>
