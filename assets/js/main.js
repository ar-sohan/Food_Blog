// Shared front-end helpers used by every page.

// Wrap XMLHttpRequest in a simple helper so the AJAX style stays consistent
// with the one taught in class (XHR, not fetch).
function ajaxRequest(method, url, body, onSuccess, onError){
    let xhttp = new XMLHttpRequest();
    xhttp.open(method, url, true);
    if(method.toUpperCase() === 'POST' || method.toUpperCase() === 'DELETE'){
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    }
    xhttp.onreadystatechange = function(){
        if(this.readyState === 4){
            if(this.status === 200){
                let data;
                try { data = JSON.parse(this.responseText); }
                catch(e){ data = this.responseText; }
                if(onSuccess){ onSuccess(data); }
            } else {
                if(onError){ onError(this.status, this.responseText); }
            }
        }
    }
    xhttp.send(body || null);
}

// Helper to escape HTML when inserting AJAX responses into the DOM.
function escapeHtml(text){
    if(text === null || text === undefined){ return ''; }
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}