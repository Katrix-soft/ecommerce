<style>
#sply-chatbot-btn{position:fixed;bottom:24px;right:6px;z-index:9999;width:clamp(48px,8vw,60px);height:clamp(48px,8vw,60px);border-radius:50%;background:#ffffff;box-shadow:0 4px 20px rgba(0,0,0,.15);display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;outline:none;transition:transform .2s ease,box-shadow .2s ease;overflow:hidden;position:fixed}
#sply-chatbot-btn:hover{transform:scale(1.12);box-shadow:0 8px 28px rgba(26,122,94,.6)}
#sply-chatbot-btn:active{transform:scale(.97)}
#sply-chatbot-btn .sply-initials{color:#fff;font-size:15px;font-weight:700;letter-spacing:.5px;font-family:'Figtree',sans-serif;user-select:none;line-height:1;position:relative;z-index:1}
#sply-chatbot-btn .sply-pulse-ring{position:absolute;width:100%;height:100%;border-radius:50%;background:rgba(42,168,126,.35);animation:sply-pulse 2s ease-out infinite}
@keyframes sply-pulse{0%{transform:scale(1);opacity:.7}70%,100%{transform:scale(1.55);opacity:0}}
@media(max-width:480px){#sply-chatbot-btn{width:48px;height:48px;bottom:12px;right:4px}#sply-chat-panel{width:calc(100vw - 16px)!important;right:8px!important}}
@media(min-width:481px) and (max-width:768px){#sply-chatbot-btn{width:54px;height:54px;bottom:16px;right:4px}}

#sply-chat-panel{position:fixed;bottom:96px;right:6px;width:360px;height:450px;max-height:75vh;background:#fff;border-radius:18px;box-shadow:0 12px 48px rgba(0,0,0,.18);display:flex;flex-direction:column;z-index:9998;overflow:hidden;transform:scale(.85) translateY(16px);transform-origin:bottom right;opacity:0;visibility:hidden;pointer-events:none;transition:transform .28s cubic-bezier(.34,1.56,.64,1),opacity .2s ease,visibility 0s linear .2s}
#sply-chat-panel.open{transform:scale(1) translateY(0);opacity:1;visibility:visible;pointer-events:all;transition:transform .28s cubic-bezier(.34,1.56,.64,1),opacity .2s ease,visibility 0s linear 0s}
#sply-chat-header{background:linear-gradient(135deg,#1a7a5e,#2aa87e);padding:14px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
#sply-chat-header .sply-h-title{color:#fff;font-weight:700;font-size:15px;font-family:'Figtree',sans-serif;display:flex;align-items:center;gap:8px}
#sply-chat-header .sply-h-title::before{content:'';display:inline-block;width:8px;height:8px;border-radius:50%;background:#7fffcb;box-shadow:0 0 6px #7fffcb;animation:sply-blink 1.4s infinite}
@keyframes sply-blink{0%,100%{opacity:1}50%{opacity:.3}}
#sply-close-btn{background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;transition:background .2s}
#sply-close-btn:hover{background:rgba(255,255,255,.3)}
#sply-chat-messages{flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;min-height:0}
#sply-chat-messages::-webkit-scrollbar{width:4px}
#sply-chat-messages::-webkit-scrollbar-thumb{background:#d0ece6;border-radius:4px}
.sply-msg{max-width:80%;padding:10px 14px;border-radius:14px;font-size:13.5px;font-family:'Figtree',sans-serif;line-height:1.5;word-break:break-word;animation:sply-fadein .2s ease}
@keyframes sply-fadein{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
.sply-msg-user{background:linear-gradient(135deg,#1a7a5e,#2aa87e);color:#fff;align-self:flex-end;border-bottom-right-radius:4px}
.sply-msg-assistant{background:#f0faf6;color:#1a2e28;align-self:flex-start;border-bottom-left-radius:4px;border:1px solid #d4ede6}
.sply-welcome{text-align:center;color:#6b8a82;font-size:12.5px;font-family:'Figtree',sans-serif;padding:10px;background:#f8fffe;border-radius:10px;border:1px solid #d4ede6}
#sply-chat-footer{padding:12px;border-top:1px solid #eaf4f0;display:flex;gap:8px;align-items:flex-end;flex-shrink:0;background:#fff}
#sply-chat-input{flex:1;resize:none;border:1.5px solid #d4ede6;border-radius:12px;padding:9px 12px;font-size:13.5px;font-family:'Figtree',sans-serif;outline:none;transition:border .2s;line-height:1.4;max-height:100px;overflow-y:auto}
#sply-chat-input:focus{border-color:#2aa87e}
#sply-send-btn{background:linear-gradient(135deg,#1a7a5e,#2aa87e);border:none;border-radius:10px;width:38px;height:38px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:transform .15s,opacity .15s}
#sply-send-btn:hover{transform:scale(1.08)}
#sply-send-btn:disabled{opacity:.4;cursor:not-allowed;transform:none}
#sply-send-btn svg{fill:#fff}
</style>

{{-- Botón flotante --}}
<button id="sply-chatbot-btn" onclick="splyToggle()" aria-label="Abrir asistente Shoply">
    <span class="sply-pulse-ring"></span>
    <img src="{{ asset('img/chatbot-logo.png.png') }}" alt="IA" style="width:52px;height:52px;position:relative;z-index:1;border-radius:50%;object-fit:cover;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.1));" />
</button>

{{-- Panel de chat --}}
<div id="sply-chat-panel" role="dialog" aria-label="Chat asistente Shoply">
    <div id="sply-chat-header">
        <div class="sply-h-title">Asistente Shoply</div>
        <button id="sply-close-btn" onclick="splyToggle()" aria-label="Cerrar">✕</button>
    </div>
    <div id="sply-chat-messages">
        <div class="sply-welcome">👋 ¡Hola! Soy el asistente de <strong>Shoply</strong>.<br>¿En qué te puedo ayudar?</div>
    </div>
    <div id="sply-chat-footer">
        <textarea id="sply-chat-input" rows="1" placeholder="Escribí tu consulta..." onkeydown="splyKey(event)"></textarea>
        <button id="sply-send-btn" onclick="splySend()" aria-label="Enviar">
            <svg width="18" height="18" viewBox="0 0 24 24"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
        </button>
    </div>
</div>

<script>
(function(){
    // Laravel API routes
    const MODELS_URL   = '/api/chatbot/models?v=' + Date.now();
    const CHAT_URL     = '/api/chatbot/chat?v=' + Date.now();
    const CLEAR_URL    = '/api/chatbot/clear-session';

    // Session ID persistente — el historial vive en Redis server-side
    let sessionId = localStorage.getItem('sply_chat_session');
    if (!sessionId) {
        sessionId = 'sess_' + Math.random().toString(36).slice(2) + Date.now().toString(36);
        localStorage.setItem('sply_chat_session', sessionId);
    }

    let isOpen  = false;
    let sending = false;
    let loaded  = false;

    window.splyToggle = function(){
        isOpen = !isOpen;
        document.getElementById('sply-chat-panel').classList.toggle('open', isOpen);
        if(isOpen){
            if(!loaded) loadModels();
            setTimeout(() => document.getElementById('sply-chat-input').focus(), 300);
        }
    };

    window.splyKey = function(e){
        if(e.key === 'Enter' && !e.shiftKey){ e.preventDefault(); splySend(); }
    };

    let splyModel = null;

    async function loadModels(){
        const input = document.getElementById('sply-chat-input');
        const btn   = document.getElementById('sply-send-btn');
        if(input) input.placeholder = 'Conectando con el asistente...';
        if(btn)   btn.disabled = true;
        try{
            const r = await fetch(MODELS_URL);
            if(!r.ok) throw new Error('HTTP ' + r.status);
            const d = await r.json();
            const models = d.models || [];
            if(models.length > 0){
                splyModel = models[0].name;
                loaded = true;
                if(input) input.placeholder = 'Escribí tu consulta...';
                if(btn)   btn.disabled = false;
            } else {
                throw new Error('Sin modelos disponibles');
            }
        }catch(e){
            console.error('Sply error:', e);
            appendMsg('assistant', '⚠️ No se pudo conectar con el asistente. Intentá recargar la página.');
            if(input) input.placeholder = 'Error de conexión';
        }
    }

    window.splySend = async function(){
        if(sending) return;
        const input = document.getElementById('sply-chat-input');
        const text  = input.value.trim();
        if(!text) return;

        // Si el modelo aún no cargó, esperar y reintentar
        if(!splyModel){
            if(!loaded) await loadModels();
            if(!splyModel){
                appendMsg('assistant', '⚠️ El asistente aún no está listo. Esperá un momento e intentá de nuevo.');
                return;
            }
        }

        sending = true;
        document.getElementById('sply-send-btn').disabled = true;
        input.value = ''; input.style.height = 'auto';

        appendMsg('user', text);

        const id = 'sply-m-' + Date.now();
        appendMsg('assistant', '...', id);

        try{
            const res = await fetch(CHAT_URL, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    model     : splyModel,
                    messages  : [{ role: 'user', content: text }],
                    session_id: sessionId,
                }),
            });

            const data = await res.json();
            console.log('Sply raw response:', JSON.stringify(data));

            if(data.error){
                throw new Error(data.error);
            }

            // El proxy devuelve { content: "..." } directamente
            // Fallbacks para formatos Ollama y OpenAI
            const reply =
                data?.content ||
                data?.message?.content ||
                data?.choices?.[0]?.message?.content ||
                data?.response ||
                '';

            const el = document.getElementById(id);
            if(el) el.textContent = reply || '(respuesta vacía)';
            scrollBottom();
        }catch(e){
            const el = document.getElementById(id);
            if(el) el.textContent = '❌ Error: ' + e.message;
        }

        sending = false;
        document.getElementById('sply-send-btn').disabled = false;
        document.getElementById('sply-chat-input').focus();
    };

    function appendMsg(role, text, id){

        const msgs = document.getElementById('sply-chat-messages');
        const d    = document.createElement('div');
        d.className  = 'sply-msg sply-msg-' + role;
        if(id) d.id  = id;
        d.textContent = text;
        msgs.appendChild(d);
        scrollBottom();
    }

    function scrollBottom(){
        const m = document.getElementById('sply-chat-messages');
        m.scrollTop = m.scrollHeight;
    }

    // Auto-resize textarea
    document.addEventListener('DOMContentLoaded', function(){
        const ta = document.getElementById('sply-chat-input');
        if(ta) ta.addEventListener('input', function(){
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Cerrar chat al hacer clic afuera
        document.addEventListener('click', function(e) {
            const panel = document.getElementById('sply-chat-panel');
            const btn = document.getElementById('sply-chatbot-btn');
            
            if (isOpen && !panel.contains(e.target) && !btn.contains(e.target)) {
                splyToggle();
            }
        });
    });
})();
</script>
