

(function(){
    let form = document.getElementById('searchForm');
    if(!form){ return; }

    let qInput        = document.getElementById('searchQ');
    let locationInput = document.getElementById('searchLocation');
    let areaInput     = document.getElementById('searchArea');
    let status        = document.getElementById('searchStatus');
    let results       = document.getElementById('searchResults');
    let defaultList   = document.getElementById('defaultList'); // only on restaurants.php

    let timer = null;

    function escapeHtml(text){
        if(text === null || text === undefined){ return ''; }
        return String(text)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function ellipsis(text, n){
        if(!text){ return ''; }
        return text.length > n ? text.substring(0, n) + '...' : text;
    }

    function renderResults(data){
        let html = '';

        if(data.restaurants.length === 0 && data.items.length === 0){
            html = '<p class="muted">No matches. Try a different search.</p>';
            results.innerHTML = html;
            return;
        }

        if(data.restaurants.length > 0){
            html += '<h3>Restaurants (' + data.restaurants.length + ')</h3>';
            html += '<div class="card-grid">';
            data.restaurants.forEach(function(r){
                html += '<a class="card" href="restaurantDetail.php?id=' + encodeURIComponent(r.id) + '">';
                html += '<h3>' + escapeHtml(r.name) + '</h3>';
                html += '<p class="muted">' + escapeHtml(r.location) + ' &middot; ' + escapeHtml(r.area) + '</p>';
                html += '<p>' + escapeHtml(ellipsis(r.short_background || '', 140)) + '</p>';
                html += '</a>';
            });
            html += '</div>';
        }

        if(data.items.length > 0){
            html += '<h3 style="margin-top:20px">Menu Items (' + data.items.length + ')</h3>';
            html += '<div class="card-grid">';
            data.items.forEach(function(m){
                html += '<a class="card" href="menuItem.php?id=' + encodeURIComponent(m.id) + '">';
                if(m.image_path){
                    html += '<img src="../assets/uploads/menu/' + encodeURIComponent(m.image_path) + '" alt="' + escapeHtml(m.name) + '">';
                }
                html += '<h3>' + escapeHtml(m.name) + '</h3>';
                html += '<p class="price">$' + (parseFloat(m.price)).toFixed(2) + '</p>';
                html += '<p class="muted">' + escapeHtml(m.restaurant_name) + ' &middot; ' + escapeHtml(m.restaurant_location || '') + '</p>';
                html += '<p>' + escapeHtml(ellipsis(m.description || '', 100)) + '</p>';
                html += '</a>';
            });
            html += '</div>';
        }

        results.innerHTML = html;
    }

    function runSearch(){
        let q   = qInput.value.trim();
        let loc = locationInput.value.trim();
        let ar  = areaInput.value.trim();

        // If everything is empty, restore the default list (on restaurants.php)
        // or just clear (on home.php).
        if(q === '' && loc === '' && ar === ''){
            results.innerHTML = '';
            status.textContent = '';
            if(defaultList){ defaultList.style.display = ''; }
            return;
        }

        if(defaultList){ defaultList.style.display = 'none'; }
        status.textContent = 'Searching...';

        let url = '../controller/apiSearch.php'
                + '?q='        + encodeURIComponent(q)
                + '&location=' + encodeURIComponent(loc)
                + '&area='     + encodeURIComponent(ar);

        let xhttp = new XMLHttpRequest();
        xhttp.open('get', url, true);
        xhttp.onreadystatechange = function(){
            if(this.readyState === 4){
                if(this.status === 200){
                    let data;
                    try { data = JSON.parse(this.responseText); }
                    catch(e){ status.textContent = 'Bad response from server.'; return; }
                    status.textContent =
                        'Found ' + data.restaurants.length + ' restaurant(s) and '
                                 + data.items.length + ' menu item(s).';
                    renderResults(data);
                } else {
                    status.textContent = 'Search failed (HTTP ' + this.status + ').';
                }
            }
        };
        xhttp.send();
    }

    function debounced(){
        clearTimeout(timer);
        timer = setTimeout(runSearch, 300);
    }

    qInput.addEventListener('input',        debounced);
    locationInput.addEventListener('input', debounced);
    areaInput.addEventListener('input',     debounced);

    form.addEventListener('submit', function(e){
        e.preventDefault();
        clearTimeout(timer);
        runSearch();
    });
})();
