<div id="stats" hx-swap-oob="true">
    @include('tasks._stats', ['stats' => $stats])
</div>

<div id="toast" hx-swap-oob="true">
    @include('tasks._toast', ['toast' => $toast ?? null])
</div>

<div id="form-feedback" hx-swap-oob="true"></div>
