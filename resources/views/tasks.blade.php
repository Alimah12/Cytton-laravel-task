<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Task Manager — UI</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      
      --bg:#f4fbf7;
      --card:#ffffff;
      --muted:#6b7280;
      --accent:#0f7a53;    
      --accent-2:#79d88a;   
      --success:#10b981;
      --danger:#ef4444;
      --radius:12px;
      --shadow: 0 6px 18px rgba(15,23,42,0.08);
    }
    *{box-sizing:border-box}
    body{font-family:Inter,system-ui,Segoe UI,Roboto,Arial;background:var(--bg);margin:0;color:#0f172a}
    header{background:linear-gradient(90deg,var(--accent),var(--accent-2));color:white;padding:28px 24px}
    header .wrap{max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between}
    header h1{margin:0;font-size:20px;letter-spacing:0.2px}
    header p{margin:0;opacity:0.9;font-size:13px}
    main{max-width:1100px;margin:28px auto;padding:0 24px}
    .grid{display:grid;grid-template-columns:1fr 360px;gap:20px}
    .card{background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow);padding:18px}
    .form-row{display:flex;gap:10px}
    input[type=text], input[type=date], select{flex:1;padding:10px;border:1px solid #e6e9ef;border-radius:8px;font-size:14px}
    button{background:var(--accent);color:white;border:none;padding:10px 14px;border-radius:8px;cursor:pointer;font-weight:600}
    button.secondary{background:linear-gradient(90deg, rgba(121,216,138,0.12), rgba(15,122,83,0.06));color:var(--accent);border:1px solid rgba(15,122,83,0.08)}
    .tasks-list{display:flex;flex-direction:column;gap:12px}
    .task{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
    .meta{color:var(--muted);font-size:13px}
    .badge{background:linear-gradient(90deg, rgba(15,122,83,0.08), rgba(121,216,138,0.08));color:var(--accent);padding:6px 8px;border-radius:999px;font-weight:700;font-size:12px}
    .controls{display:flex;gap:8px;align-items:center}
    .empty{padding:30px;text-align:center;color:var(--muted)}
    .footer-note{margin-top:12px;color:var(--muted);font-size:13px}
    @media (max-width:900px){.grid{grid-template-columns:1fr}.form-row{flex-direction:column}}
    /* subtle hover */
    .task-card{padding:12px;border-radius:10px;border:1px solid #eef2f8;background:linear-gradient(0deg,#fff,#fff);transition:transform .12s ease,box-shadow .12s ease}
    .task-card:hover{transform:translateY(-4px);box-shadow:0 10px 30px rgba(2,6,23,0.06)}
    .muted{color:var(--muted)}
    .toast{position:fixed;right:20px;bottom:20px;background:#111827;color:white;padding:10px 14px;border-radius:8px;box-shadow:var(--shadow)}
  </style>
</head>
<body>
  <header>
    <div class="wrap">
      <div>
        <h1>Task Manager</h1>
        <p class="muted">Manage tasks — connected to Clever Cloud MySQL, deployed on Render</p>
      </div>
      <div class="muted">Live: <a href="https://cytton-laravel-task.onrender.com" style="color:rgba(255,255,255,0.9);text-decoration:underline">cytton-laravel-task.onrender.com</a></div>
    </div>
  </header>

  <main>
    <div class="grid">
      <div>
        <div class="card">
          <h2 style="margin-top:0">Tasks</h2>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div class="controls">
              <select id="filter_status">
                <option value="">All statuses</option>
                <option value="pending">pending</option>
                <option value="in_progress">in_progress</option>
                <option value="done">done</option>
              </select>
              <button class="secondary" id="refresh">Refresh</button>
            </div>
            <div class="meta">API: <code class="badge">/api/v1/tasks</code></div>
          </div>

          <div id="tasks" class="tasks-list"></div>

          <div id="empty" class="empty" style="display:none">No tasks yet — create one from the panel on the right.</div>
        </div>

        <div class="footer-note">Registered endpoints: <br/>
          <small class="muted">GET api/test · GET api/v1/tasks · POST api/v1/tasks · GET api/v1/tasks/report · PATCH api/v1/tasks/{id}/status · DELETE api/v1/tasks/{id}</small>
        </div>
      </div>

      <aside>
        <div class="card">
          <h3 style="margin-top:0">Create Task</h3>
          <div class="form-row" style="margin-bottom:10px">
            <input id="title" type="text" placeholder="Title" />
          </div>
          <div class="form-row" style="margin-bottom:10px">
            <input id="due_date" type="date" />
            <select id="priority">
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>
          <div style="display:flex;gap:8px">
            <button id="create">Create Task</button>
            <button class="secondary" id="create_example">Seed Example</button>
          </div>
          <p class="muted" style="margin-top:10px">Create tasks, advance status, and delete (only when status is <strong>done</strong>).</p>
        </div>
      </aside>
    </div>
  </main>

  <div id="toast" class="toast" style="display:none"></div>

  <script>
    const base = '/api/v1';

    
    const toastEl = document.getElementById('toast');
    function showToast(message, type = 'info', ms = 3000){
      toastEl.innerText = message;
      toastEl.style.display = 'block';
      toastEl.style.background = type === 'error' ? '#b91c1c' : (type === 'success' ? '#065f46' : '#111827');
      setTimeout(()=>{ toastEl.style.display = 'none' }, ms);
    }

    
    function makeSpinner(){
      const s = document.createElement('span');
      s.className = 'spinner';
      s.style.width = '14px'; s.style.height = '14px'; s.style.border = '2px solid rgba(255,255,255,0.35)';
      s.style.borderTopColor = 'white'; s.style.borderRadius = '50%'; s.style.display = 'inline-block';
      s.style.verticalAlign = 'middle'; s.style.marginRight = '8px';
      s.style.animation = 'spin .8s linear infinite';
      return s;
    }

    
    const styleSheet = document.createElement('style');
    styleSheet.innerHTML = '@keyframes spin{to{transform:rotate(360deg)}}';
    document.head.appendChild(styleSheet);

    function setLoading(button, loading, label){
      if(loading){
        button.dataset.orig = button.innerHTML;
        button.disabled = true;
        
        const spinner = makeSpinner();
        button.innerHTML = ''; button.appendChild(spinner); button.appendChild(document.createTextNode(label||'Working'));
      } else {
        button.disabled = false;
        if(button.dataset.orig) button.innerHTML = button.dataset.orig;
      }
    }

    async function fetchTasks(){
      const status = document.getElementById('filter_status').value;
      const url = status ? `${base}/tasks?status=${status}` : `${base}/tasks`;
      try{
        const res = await fetch(url);
        if(!res.ok) throw new Error('Failed to fetch tasks');
        const data = await res.json();
        renderTasks(Array.isArray(data)?data:[]);
      } catch (e){ renderTasks([]); showToast('Unable to load tasks', 'error'); }
    }

    function renderTasks(tasks){
      const el = document.getElementById('tasks');
      const empty = document.getElementById('empty');
      el.innerHTML='';
      if(!tasks.length){ empty.style.display='block'; return }
      empty.style.display='none';
      tasks.forEach(t=>{
        const card = document.createElement('div'); card.className='task-card task';
        const left = document.createElement('div');
        left.innerHTML = `<div style="font-weight:700">${escapeHtml(t.title)}</div><div class='meta'>Due: ${t.due_date||'—'} · <span class='muted'>${t.priority}</span></div>`;
        const right = document.createElement('div'); right.style.textAlign='right';
        const status = document.createElement('div'); status.innerHTML = `<span class='badge'>${t.status}</span>`;
        const btns = document.createElement('div'); btns.style.marginTop='8px';

        const toNext = document.createElement('button'); toNext.innerText='Advance';
        toNext.onclick = async ()=>{
          const next = t.status === 'pending' ? 'in_progress' : (t.status === 'in_progress'? 'done': null);
          if(!next) return showToast('Cannot advance status', 'error');
          setLoading(toNext, true, 'Updating');
          try{
            const r = await fetch(`${base}/tasks/${t.id}/status`,{method:'PATCH',headers:{'Content-Type':'application/json'},body:JSON.stringify({status:next})});
            if(r.ok){ showToast('Status updated', 'success'); fetchTasks(); } else { const err = await r.json().catch(()=>null); showToast(err?.message||'Failed to update', 'error'); }
          } catch(e){ showToast('Failed to update', 'error'); }
          setLoading(toNext, false);
        };

        const del = document.createElement('button'); del.innerText='Delete'; del.style.marginLeft='8px'; del.style.background='#ef4444';
        del.onclick = async ()=>{
          if(!confirm('Delete this task?')) return;
          setLoading(del, true, 'Deleting');
          try{
            const r = await fetch(`${base}/tasks/${t.id}`,{method:'DELETE'});
            if(r.status===204){ showToast('Deleted', 'success'); fetchTasks(); } else { const err = await r.json().catch(()=>null); showToast(err?.message||'Failed to delete', 'error'); }
          } catch(e){ showToast('Failed to delete', 'error'); }
          setLoading(del, false);
        };

        btns.appendChild(toNext); btns.appendChild(del);
        right.appendChild(status); right.appendChild(btns);
        card.appendChild(left); card.appendChild(right);
        el.appendChild(card);
      })
    }

    document.getElementById('create').onclick = async ()=>{
      const btn = document.getElementById('create');
      const title = document.getElementById('title').value.trim();
      const due_date = document.getElementById('due_date').value||null;
      const priority = document.getElementById('priority').value;
      if(!title) return showToast('Title is required', 'error');
      setLoading(btn, true, 'Creating');
      try{
        const r = await fetch(base+'/tasks',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({title,due_date,priority})});
        if(r.status===201){ document.getElementById('title').value=''; document.getElementById('due_date').value=''; showToast('Created', 'success'); fetchTasks(); } else { const err = await r.json().catch(()=>null); showToast(err?.message||'Create failed', 'error'); }
      } catch(e){ showToast('Create failed', 'error'); }
      setLoading(btn, false);
    };

    document.getElementById('refresh').onclick = async ()=>{
      const btn = document.getElementById('refresh'); setLoading(btn, true, 'Refreshing'); await fetchTasks(); setLoading(btn, false);
    };

    document.getElementById('create_example').onclick = async ()=>{
      const btn = document.getElementById('create_example'); setLoading(btn, true, 'Seeding');
      try{
        const payload = {title:'Interview: Prepare slides',due_date:null,priority:'high'};
        await fetch(base+'/tasks',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
        showToast('Example created', 'success');
        fetchTasks();
      } catch(e){ showToast('Failed', 'error'); }
      setLoading(btn, false);
    };

    function escapeHtml(s){ if(!s) return ''; return s.replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c])); }

    
    fetchTasks();
  </script>
</body>
</html>
