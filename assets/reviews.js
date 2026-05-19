
(function(){
    let section = document.getElementById('reviews');
    if(!section){ return; }

    let menuItemId = section.getAttribute('data-menu-item-id');
    let csrf       = section.getAttribute('data-csrf');
    let list       = document.getElementById('reviewList');
    let emptyMsg   = document.getElementById('noReviewsMsg');
    let counter    = document.getElementById('reviewCount');
    let form       = document.getElementById('reviewForm');

    function escapeHtml(text){
        if(text === null || text === undefined){ return ''; }
        return String(text)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function updateCount(delta){
        let n = parseInt(counter.textContent, 10) + delta;
        counter.textContent = n;
        emptyMsg.style.display = n === 0 ? '' : 'none';
    }

    function bindDelete(btn){
        btn.addEventListener('click', function(){
            if(!confirm('Delete your review?')){ return; }
            let id = btn.getAttribute('data-id');

            let url = '../controller/apiReviewDelete.php?id=' + encodeURIComponent(id)
                    + '&csrf=' + encodeURIComponent(csrf);

            let xhttp = new XMLHttpRequest();
            xhttp.open('delete', url, true);
            xhttp.onreadystatechange = function(){
                if(this.readyState === 4){
                    let data = {};
                    try { data = JSON.parse(this.responseText); } catch(e){}
                    if(this.status === 200 && data.success){
                        let li = document.getElementById('review-' + id);
                        if(li){ li.remove(); }
                        updateCount(-1);
                    } else {
                        alert('Could not delete: ' + (data.error || 'unknown error'));
                    }
                }
            };
            xhttp.send();
        });
    }

    // Wire delete buttons that were already on the page (server-rendered).
    document.querySelectorAll('.js-delete-review').forEach(bindDelete);

    // Wire the post form (only present for logged-in members).
    if(form){
        let textarea = document.getElementById('reviewComment');
        let errorBox = document.getElementById('reviewError');

        form.addEventListener('submit', function(e){
            e.preventDefault();
            errorBox.textContent = '';

            let comment = textarea.value.trim();
            if(comment === ''){
                errorBox.textContent = 'Please type a comment before posting.';
                return;
            }
            if(comment.length > 1000){
                errorBox.textContent = 'Comment must be 1000 characters or fewer.';
                return;
            }

            let body = 'menu_item_id=' + encodeURIComponent(menuItemId)
                     + '&comment='     + encodeURIComponent(comment)
                     + '&csrf='        + encodeURIComponent(csrf);

            let xhttp = new XMLHttpRequest();
            xhttp.open('post', '../controller/apiReviewAdd.php', true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhttp.onreadystatechange = function(){
                if(this.readyState === 4){
                    let data = {};
                    try { data = JSON.parse(this.responseText); } catch(e){}
                    if(this.status === 200 && data.success){
                        let r = data.review;
                        let li = document.createElement('li');
                        li.className = 'review';
                        li.id = 'review-' + r.id;
                        li.innerHTML =
                            '<div class="review-head">' +
                                '<strong>' + escapeHtml(r.user_name) + '</strong>' +
                                ' <span class="muted">&middot; ' + escapeHtml(r.created_at) + '</span>' +
                            '</div>' +
                            '<div class="review-body">' + escapeHtml(r.comment).replace(/\n/g, '<br>') + '</div>' +
                            '<button class="btn btn-small btn-danger js-delete-review" data-id="' + r.id + '">Delete</button>';
                        list.prepend(li);
                        bindDelete(li.querySelector('.js-delete-review'));
                        textarea.value = '';
                        updateCount(+1);
                    } else {
                        errorBox.textContent = data.error || 'Could not post review.';
                    }
                }
            };
            xhttp.send(body);
        });
    }
})();
