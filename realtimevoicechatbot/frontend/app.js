(function(){
  // Prefer the PHP server backend; fall back to relative for same-origin hosting
  const API_BASES = [
    'http://localhost:8080/backend',
    '../backend'
  ];

  async function tryFetch(path, options){
    let lastErr;
    for (const base of API_BASES){
      try {
        const res = await fetch(`${base}/${path}`, options);
        const text = await res.text();
        try { return { base, res, data: JSON.parse(text) }; } catch(_) { return { base, res, data: { ok:false, error: `Invalid JSON from ${path}`, raw:text } }; }
      } catch (e) {
        lastErr = e;
      }
    }
    throw lastErr || new Error('All API bases failed');
  }

  async function postJson(path, body) {
    const { data } = await tryFetch(path, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(body)
    });
    return data;
  }

  async function getJson(path) {
    const { data } = await tryFetch(path, { credentials: 'include' });
    return data;
  }

  function $(id){ return document.getElementById(id); }

  // Login/Register
  const isRegister = window.__IS_REGISTER__;
  // Remove auth: hide login/register flows and redirect to dashboard
  const loginForm = $('login-form');
  if (loginForm) { location.href = 'dashboard.html'; }
  const registerForm = $('register-form');
  if (registerForm) { location.href = 'dashboard.html'; }

  // Dashboard record/upload
  const recordBtn = $('record-btn');
  const stopBtn = $('stop-btn');
  const submitBtn = $('submit-btn');
  const cancelBtn = $('cancel-btn');
  const fileInput = $('file-input');
  const uploadBtn = $('upload-btn');
  const preview = $('preview');
  const recordingStatus = $('recording-status');
  const status = $('status');
  const result = $('result');
  const logoutBtn = null;
  let mediaRecorder; let chunks = []; let recordedBlob = null;

  // logout removed

  async function refreshNotes(containerId='notes'){
    const out = await getJson('fetch_notes.php');
    const el = $(containerId);
    if (!el) return;
    if (!out.ok) { el.textContent = out.error || 'Failed to load notes'; return; }
    el.innerHTML = '';
    (out.notes||[]).forEach(n=>{
      const card = document.createElement('div');
      card.className = 'note';
      const ai = typeof n.ai_output === 'string' ? safeParse(n.ai_output) : (n.ai_output||{});
      const text = JSON.stringify(ai, null, 2);
      card.textContent = text;
      el.appendChild(card);
    });
  }

  function safeParse(s){ try { return JSON.parse(s); } catch(e){ return { raw: s }; } }

  async function uploadBlob(blob){
    status.textContent = 'Processing audio...';
    const fd = new FormData();
    fd.append('audio', blob, 'recording.webm');
    try {
      const { data: out } = await tryFetch('upload_audio.php', { method:'POST', body: fd, credentials:'include' });
      if (out.ok) {
        status.textContent = 'Success! Medical notes generated.';
        result.textContent = JSON.stringify(out.note.ai_output || out.note, null, 2);
        await refreshNotes();
      } else {
        status.textContent = out.error || 'Upload failed';
      }
    } catch (e) {
      status.textContent = 'Network error: ' + e.message;
    }
  }

  if (recordBtn && stopBtn) {
    recordBtn.addEventListener('click', async ()=>{
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        chunks = [];
        mediaRecorder.ondataavailable = e=>{ if (e.data.size > 0) chunks.push(e.data); };
        mediaRecorder.onstop = ()=>{
          recordedBlob = new Blob(chunks, { type: 'audio/webm' });
          preview.src = URL.createObjectURL(recordedBlob);
          preview.classList.remove('hidden');
          // Show submit/cancel buttons instead of auto-uploading
          submitBtn.disabled = false;
          cancelBtn.disabled = false;
          recordingStatus.textContent = 'Recording complete. Review and submit or cancel.';
        };
        mediaRecorder.start();
        recordBtn.disabled = true; 
        stopBtn.disabled = false; 
        submitBtn.disabled = true;
        cancelBtn.disabled = true;
        recordingStatus.textContent = 'Recording...';
      } catch (e) {
        recordingStatus.textContent = 'Microphone access denied';
      }
    });
    stopBtn.addEventListener('click', ()=>{
      if (mediaRecorder && mediaRecorder.state !== 'inactive') mediaRecorder.stop();
      recordBtn.disabled = false; 
      stopBtn.disabled = true; 
      recordingStatus.textContent = 'Recording stopped.';
    });
  }

  // Submit button - process the recorded audio
  if (submitBtn) {
    submitBtn.addEventListener('click', async ()=>{
      if (!recordedBlob) {
        recordingStatus.textContent = 'No recording to submit';
        return;
      }
      submitBtn.disabled = true;
      cancelBtn.disabled = true;
      recordingStatus.textContent = 'Submitting...';
      await uploadBlob(recordedBlob);
      // Reset after successful upload
      recordedBlob = null;
      submitBtn.disabled = true;
      cancelBtn.disabled = true;
      recordingStatus.textContent = '';
    });
  }

  // Cancel button - clear the recording
  if (cancelBtn) {
    cancelBtn.addEventListener('click', ()=>{
      recordedBlob = null;
      preview.classList.add('hidden');
      submitBtn.disabled = true;
      cancelBtn.disabled = true;
      recordingStatus.textContent = 'Recording cancelled';
      // Reset buttons
      recordBtn.disabled = false;
      stopBtn.disabled = true;
    });
  }

  if (uploadBtn && fileInput) {
    uploadBtn.addEventListener('click', async ()=>{
      const f = fileInput.files && fileInput.files[0];
      if (!f) { status.textContent = 'Choose a file first'; return; }
      preview.src = URL.createObjectURL(f);
      preview.classList.remove('hidden');
      await uploadBlob(f);
    });
  }

  // Notes page
  if (document.location.pathname.endsWith('notes.html')) {
    refreshNotes('notes');
  }

  // Dashboard notes preview
  if (document.location.pathname.endsWith('dashboard.html')) {
    refreshNotes('notes');
  }
})();


