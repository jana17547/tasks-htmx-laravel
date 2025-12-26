<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Tasks++ (Laravel + HTMX)</title>

    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
    <script src="https://unpkg.com/htmx.org@1.9.12/dist/ext/morphdom-swap.js"></script>

    <style>
        :root{
            --bg0:#07080c;
            --bg1:#0b0c12;
            --card: rgba(255,255,255,.045);
            --card2: rgba(255,255,255,.06);
            --line: rgba(255,255,255,.09);
            --text:#eef1f7;
            --muted:#9aa3b2;
            --accent:#6e7cff;
            --ok:#39d98a;
            --warn:#ffcc66;
            --bad:#ff6b6b;
            --shadow: 0 20px 70px rgba(0,0,0,.55);
        }

        *{box-sizing:border-box}
        body{
            margin:0;
            color:var(--text);
            font-family: ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial;
            background:
                radial-gradient(1100px 500px at 15% 5%, rgba(110,124,255,.18), transparent 60%),
                radial-gradient(900px 500px at 85% 15%, rgba(57,217,138,.10), transparent 65%),
                radial-gradient(900px 600px at 50% 120%, rgba(255,204,102,.10), transparent 60%),
                linear-gradient(180deg, var(--bg0), var(--bg1));
            min-height:100vh;
        }

        .wrap{max-width:900px;margin:46px auto;padding:0 16px}
        .topbar{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:14px}
        h1{margin:0;font-size:34px;letter-spacing:.2px}
        .sub{color:var(--muted);font-size:13px;line-height:1.35}
        .kbd{
            display:inline-flex;align-items:center;gap:6px;
            border:1px solid var(--line);
            background:rgba(0,0,0,.25);
            border-radius:999px;
            padding:6px 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,.28);
        }
        .dot{width:7px;height:7px;border-radius:50%;background:var(--accent);box-shadow:0 0 16px rgba(110,124,255,.65)}

        .grid{display:grid;gap:14px}
        .card{
            background:linear-gradient(180deg, var(--card), var(--card2));
            border:1px solid var(--line);
            border-radius:22px;
            padding:16px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }

        .row{display:flex;gap:10px;align-items:center}
        .row > *{flex:1}
        input,select{
            width:100%;
            padding:12px 14px;
            border-radius:14px;
            border:1px solid var(--line);
            background:rgba(0,0,0,.30);
            color:var(--text);
            outline:none;
            transition: border-color .18s ease, transform .18s ease, background .18s ease;
        }
        input:focus,select:focus{
            border-color: rgba(110,124,255,.55);
            background: rgba(0,0,0,.36);
        }

        .btn{
            flex:0 0 auto;
            border-radius:14px;
            border:1px solid var(--line);
            background:rgba(0,0,0,.30);
            color:var(--text);
            padding:11px 14px;
            cursor:pointer;
            transition: transform .16s ease, border-color .16s ease, background .16s ease;
            user-select:none;
        }
        .btn:hover{transform: translateY(-1px); border-color: rgba(110,124,255,.45); background:rgba(0,0,0,.38)}
        .btn:active{transform: translateY(0px) scale(.99)}
        .btn.primary{border-color: rgba(110,124,255,.45)}
        .btn.danger:hover{border-color: rgba(255,107,107,.55)}
        .btn.ghost{background:transparent}

        .pillbar{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
        .pill{
            border-radius:999px;
            padding:8px 12px;
            border:1px solid var(--line);
            background:rgba(0,0,0,.22);
            color:var(--text);
        }
        .pill.active{border-color: rgba(110,124,255,.65); box-shadow:0 0 0 3px rgba(110,124,255,.12)}
        .spacer{flex:1}

        .divider{height:1px;background:rgba(255,255,255,.07);margin:12px 0}

        /* Stats */
        .stats{display:flex;flex-wrap:wrap;gap:8px;align-items:center;justify-content:space-between}
        .chips{display:flex;gap:8px;flex-wrap:wrap}
        .chip{
            font-size:12px;color:var(--muted);
            border:1px solid var(--line);
            background:rgba(0,0,0,.22);
            padding:6px 10px;border-radius:999px;
        }
        .chip b{color:var(--text);font-weight:700}
        .bar{
            width:210px;height:10px;border-radius:999px;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.08);
            overflow:hidden;
        }
        .bar > span{
            display:block;height:100%;
            width:0%;
            background: linear-gradient(90deg, rgba(110,124,255,.9), rgba(57,217,138,.8));
            border-radius:999px;
            transition: width .4s ease;
        }

        /* List */
        ul{list-style:none;padding:0;margin:0}
        li.task{
            display:flex;justify-content:space-between;gap:10px;align-items:flex-start;
            border:1px solid rgba(255,255,255,.08);
            border-radius:18px;
            padding:12px;
            margin-top:10px;
            background:rgba(0,0,0,.26);
            transition: transform .16s ease, border-color .16s ease, background .16s ease;
        }
        li.task:hover{transform: translateY(-1px); border-color: rgba(110,124,255,.30); background:rgba(0,0,0,.32)}
        .left{display:flex;gap:10px;align-items:flex-start;min-width:0}
        .title{font-size:15px;line-height:1.25;word-break:break-word}
        .done .title{opacity:.65;text-decoration:line-through}
        .meta{display:flex;gap:8px;margin-top:7px;flex-wrap:wrap}
        .badge{
            font-size:12px;padding:4px 9px;border-radius:999px;
            border:1px solid rgba(255,255,255,.10);
            color:var(--muted);
            background:rgba(0,0,0,.16);
        }
        .b-high{color:#ffd1d1;border-color:rgba(255,107,107,.35)}
        .b-low{color:#cfd6ff;border-color:rgba(110,124,255,.30)}
        .b-due{color:#ffe3b3;border-color:rgba(255,204,102,.35)}
        .b-over{color:#ffc1c1;border-color:rgba(255,107,107,.45)}

        .iconbtn{
            width:40px;height:40px;display:grid;place-items:center;flex:0 0 auto;
            border-radius:14px;border:1px solid rgba(255,255,255,.10);
            background:rgba(0,0,0,.22);cursor:pointer;
            transition: transform .16s ease, border-color .16s ease, background .16s ease;
        }
        .iconbtn:hover{transform:translateY(-1px);border-color:rgba(110,124,255,.35);background:rgba(0,0,0,.30)}
        .iconbtn:active{transform:translateY(0px) scale(.99)}

        /* Toast */
        #toast{position:fixed;left:16px;right:16px;bottom:16px;display:flex;justify-content:center;pointer-events:none;z-index:9998}
        .toast{
            pointer-events:auto;max-width:880px;width:100%;
            background:rgba(0,0,0,.50);
            border:1px solid rgba(255,255,255,.12);
            border-radius:18px;
            padding:12px 14px;
            display:flex;justify-content:space-between;gap:12px;align-items:center;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
            animation: toastIn .18s ease-out;
        }
        @keyframes toastIn { from {transform:translateY(8px);opacity:.0} to {transform:translateY(0);opacity:1} }
        .toast.success{border-color:rgba(57,217,138,.35)}
        .toast.danger{border-color:rgba(255,107,107,.40)}

        .alert{margin-top:10px;background:rgba(140,90,255,.12);border:1px solid rgba(140,90,255,.22);padding:10px 12px;border-radius:14px;color:#eadcff}

        /* WOW: HTMX swap animations */
        .htmx-added{ animation: popIn .18s ease-out; }
        @keyframes popIn { from {transform:translateY(8px);opacity:.0} to {transform:translateY(0);opacity:1} }

        /* works for non-delete swaps */
        .htmx-swapping{ opacity:0; transform:translateY(-6px); transition: opacity .16s ease, transform .16s ease; }
        .htmx-settling{ }

        /* Global loading indicator */
        .htmx-indicator{
            position:fixed; top:16px; right:16px;
            padding:10px 12px; border-radius:14px;
            border:1px solid rgba(255,255,255,.12);
            background:rgba(0,0,0,.45);
            opacity:0; transition:.2s; z-index:9999;
            color:var(--muted); font-size:13px;
            box-shadow: var(--shadow);
            display:flex; align-items:center; gap:10px;
            backdrop-filter: blur(12px);
        }
        .spinner{
            width:14px; height:14px; border-radius:50%;
            border:2px solid rgba(255,255,255,.15);
            border-top-color: rgba(255,255,255,.75);
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg);} }
        .htmx-request .htmx-indicator{opacity:1;}

        @media (prefers-reduced-motion: reduce){
            *{animation:none!important;transition:none!important}
        }
    </style>

    <script>
        // CSRF for hx requests
        document.addEventListener('htmx:configRequest', (e) => {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (token) e.detail.headers['X-CSRF-TOKEN'] = token;
        });

        // Reset add form only on successful request
        document.addEventListener('htmx:afterRequest', (e) => {
            const form = document.getElementById('add-task-form');
            if (form && e.target === form && e.detail.successful) form.reset();
        });

        // Error toast on failed responses
        document.addEventListener('htmx:responseError', (e) => {
            const toast = document.getElementById('toast');
            const code = e.detail.xhr?.status ?? 'ERR';
            toast.innerHTML = `
                <div class="toast danger">
                    <div>Greška (${code}). Proveri Network/laravel.log (CSRF ili server error).</div>
                    <button class="btn ghost" onclick="document.getElementById('toast').innerHTML=''">OK</button>
                </div>
            `;
            autoHideToast();
        });

        // Auto-hide toast
        function autoHideToast() {
            window.__toastTimer && clearTimeout(window.__toastTimer);
            window.__toastTimer = setTimeout(() => {
                const t = document.getElementById('toast');
                if (t) t.innerHTML = '';
            }, 3200);
        }
        document.addEventListener('htmx:afterSwap', (e) => {
            if (e.target && e.target.id === 'toast') autoHideToast();
        });
    </script>
</head>

<body hx-indicator="#loading" hx-ext="morphdom-swap">
<div class="wrap">
    <div class="topbar">
        <div>
            <h1>Tasks++</h1>
            <div class="sub">
                Smart add:
                <span class="kbd"><span class="dot"></span> <b>!</b>=high</span>
                <span class="kbd"><span class="dot" style="background:var(--warn)"></span> <b>?</b>=low</span>
                <span class="kbd"><span class="dot" style="background:var(--ok)"></span> <b>@YYYY-MM-DD</b>=due</span>
            </div>
        </div>
    </div>

    <div class="grid">
        {{-- ADD --}}
        <div class="card">
            <form id="add-task-form" class="row"
                  hx-post="{{ route('tasks.store') }}"
                  hx-target="#task-list"
                  hx-swap="afterbegin settle:120ms"
                  hx-disabled-elt="find button">
                @csrf
                <input type="text" name="title" placeholder="npr: ! Kupiti ulje @2026-01-10" required maxlength="120">
                <button class="btn primary" type="submit">Add</button>
            </form>
            <div id="form-feedback"></div>
        </div>

        {{-- SEARCH / FILTER --}}
        <div class="card">
            <div class="row" style="margin-bottom:12px;">
                <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Live search…"
                       hx-get="{{ route('tasks.index') }}"
                       hx-trigger="keyup changed delay:220ms"
                       hx-target="#tasks-region"
                       hx-swap="morphdom settle:80ms"
                       hx-include="#status"
                       hx-push-url="true"
                       hx-sync="this:replace">
                <input type="hidden" id="status" name="status" value="{{ $status }}">
            </div>

            @php $pill = fn($s) => $status === $s ? 'pill active' : 'pill'; @endphp

            <div class="pillbar">
                <button type="button" class="pill {{ $pill('all') }}"
                        onclick="document.getElementById('status').value='all'"
                        hx-get="{{ route('tasks.index') }}"
                        hx-vals='{"status":"all"}'
                        hx-include="#q"
                        hx-target="#tasks-region"
                        hx-swap="morphdom settle:80ms"
                        hx-push-url="true">All</button>

                <button type="button" class="pill {{ $pill('active') }}"
                        onclick="document.getElementById('status').value='active'"
                        hx-get="{{ route('tasks.index') }}"
                        hx-vals='{"status":"active"}'
                        hx-include="#q"
                        hx-target="#tasks-region"
                        hx-swap="morphdom settle:80ms"
                        hx-push-url="true">Active</button>

                <button type="button" class="pill {{ $pill('done') }}"
                        onclick="document.getElementById('status').value='done'"
                        hx-get="{{ route('tasks.index') }}"
                        hx-vals='{"status":"done"}'
                        hx-include="#q"
                        hx-target="#tasks-region"
                        hx-swap="morphdom settle:80ms"
                        hx-push-url="true">Done</button>

                <div class="spacer"></div>

                <button type="button" class="pill"
                        hx-delete="{{ route('tasks.clearCompleted') }}"
                        hx-target="#tasks-region"
                        hx-swap="morphdom settle:80ms"
                        hx-include="#q,#status"
                        hx-confirm="Obrisati sve završene taskove?">Clear completed</button>
            </div>

            <div class="divider"></div>

            <div id="tasks-region">
                @include('tasks._region', compact('tasks','stats','status','q'))
            </div>
        </div>
    </div>
</div>

<div id="loading" class="htmx-indicator">
    <span class="spinner"></span>
    <span>Učitavanje...</span>
</div>

<div id="toast"></div>
</body>
</html>
