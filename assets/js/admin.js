function adminShowError(formEl, message){
    let box = formEl.querySelector('.js-errors');
    if(!box){
        box = document.createElement('div');
        box.className = 'flash error js-errors';
        formEl.querySelector('fieldset').prepend(box);
    }
    box.innerHTML = '';
    let line = document.createElement('div');
    line.textContent = message;
    box.appendChild(line);
}

function adminClearErrors(formEl){
    let box = formEl.querySelector('.js-errors');
    if(box){ box.remove(); }
}

// ---------- restaurant create/edit form ---------------------------------

let restaurantForm = document.getElementById('restaurantForm');
if(restaurantForm){
    restaurantForm.addEventListener('submit', function(e){
        adminClearErrors(restaurantForm);
        let name     = document.getElementById('name').value.trim();
        let location = document.getElementById('location').value.trim();
        let area     = document.getElementById('area').value.trim();
        if(name === ''){     e.preventDefault(); adminShowError(restaurantForm, 'Name is required.'); return; }
        if(location === ''){ e.preventDefault(); adminShowError(restaurantForm, 'Location is required.'); return; }
        if(area === ''){     e.preventDefault(); adminShowError(restaurantForm, 'Area is required.'); return; }
    });
}

// ---------- menu item create/edit form ----------------------------------

let menuItemForm = document.getElementById('menuItemForm');
if(menuItemForm){
    menuItemForm.addEventListener('submit', function(e){
        adminClearErrors(menuItemForm);
        let name  = document.getElementById('name').value.trim();
        let price = parseFloat(document.getElementById('price').value);
        let img   = document.getElementById('image');

        if(name === ''){
            e.preventDefault(); adminShowError(menuItemForm, 'Name is required.'); return;
        }
        if(isNaN(price) || price <= 0){
            e.preventDefault(); adminShowError(menuItemForm, 'Price must be a number greater than 0.'); return;
        }
        if(img && img.files && img.files.length > 0){
            let f = img.files[0];
            if(!['image/jpeg','image/png'].includes(f.type)){
                e.preventDefault(); adminShowError(menuItemForm, 'Image must be JPEG or PNG.'); return;
            }
            if(f.size > 2 * 1024 * 1024){
                e.preventDefault(); adminShowError(menuItemForm, 'Image must be 2 MB or less.'); return;
            }
        }
    });
}

// ---------- inline AJAX delete of menu items (admin/menuItems.php) ------

let deleteButtons = document.querySelectorAll('.js-delete-menu-item');
deleteButtons.forEach(function(btn){
    btn.addEventListener('click', function(){
        if(!confirm('Delete this menu item? This cannot be undone.')){ return; }

        let id   = btn.getAttribute('data-id');
        let csrf = btn.getAttribute('data-csrf');

        let xhttp = new XMLHttpRequest();
        xhttp.open('post', '../../controller/apiAdminDeleteMenuItem.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.onreadystatechange = function(){
            if(this.readyState === 4){
                let data = {};
                try { data = JSON.parse(this.responseText); } catch(e){}
                if(this.status === 200 && data.success){
                    let row = document.getElementById('menuItem-row-' + id);
                    if(row){ row.remove(); }
                } else {
                    alert('Could not delete: ' + (data.error || 'unknown error'));
                }
            }
        };
        xhttp.send('id=' + encodeURIComponent(id) + '&csrf=' + encodeURIComponent(csrf));
    });
});