// Client-side validation + AJAX helpers for signup, login and profile forms.
// Uses the XHR pattern taught in class ( ajax/script.js in repo).

// ---------- shared helpers ----------------------------------------------

function isValidEmail(value){
    // Simple but practical pattern; server still runs FILTER_VALIDATE_EMAIL.
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function showError(formEl, message){
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

function clearErrors(formEl){
    let box = formEl.querySelector('.js-errors');
    if(box){ box.remove(); }
}

// ---------- signup form -------------------------------------------------

let signupForm = document.getElementById('signupForm');
if(signupForm){
    let emailInput  = document.getElementById('email');
    let emailStatus = document.getElementById('emailStatus');

    // AJAX: check that the email is not already in use.
    function checkEmail(){
        let email = emailInput.value.trim();
        if(email === ''){ emailStatus.textContent = ''; return; }
        if(!isValidEmail(email)){
            emailStatus.textContent = 'Email format looks wrong.';
            emailStatus.style.color = '#c0392b';
            return;
        }

        let xhttp = new XMLHttpRequest();
        xhttp.open('post', '../controller/apiCheckEmail.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.onreadystatechange = function(){
            if(this.readyState === 4 && this.status === 200){
                let data;
                try { data = JSON.parse(this.responseText); }
                catch(e){ return; }
                if(data.available){
                    emailStatus.textContent = 'Email is available.';
                    emailStatus.style.color = '#3c763d';
                } else {
                    emailStatus.textContent = 'Email is already registered.';
                    emailStatus.style.color = '#c0392b';
                }
            }
        };
        xhttp.send('email=' + encodeURIComponent(email));
    }

    if(emailInput){
        emailInput.addEventListener('blur', checkEmail);
    }

    signupForm.addEventListener('submit', function(e){
        clearErrors(signupForm);

        let name     = document.getElementById('name').value.trim();
        let email    = emailInput.value.trim();
        let password = document.getElementById('password').value;
        let password2= document.getElementById('password2').value;

        if(name === ''){ e.preventDefault(); showError(signupForm, 'Name is required.'); return; }
        if(!isValidEmail(email)){ e.preventDefault(); showError(signupForm, 'Please enter a valid email.'); return; }
        if(password.length < 8){ e.preventDefault(); showError(signupForm, 'Password must be at least 8 characters.'); return; }
        if(password !== password2){ e.preventDefault(); showError(signupForm, 'Passwords do not match.'); return; }
    });
}

// ---------- login form --------------------------------------------------

let loginForm = document.getElementById('loginForm');
if(loginForm){
    loginForm.addEventListener('submit', function(e){
        clearErrors(loginForm);
        let email    = document.getElementById('email').value.trim();
        let password = document.getElementById('password').value;
        if(!isValidEmail(email)){ e.preventDefault(); showError(loginForm, 'Please enter a valid email.'); return; }
        if(password === ''){ e.preventDefault(); showError(loginForm, 'Password is required.'); return; }
    });
}

// ---------- profile form ------------------------------------------------

let profileForm = document.getElementById('profileForm');
if(profileForm){
    profileForm.addEventListener('submit', function(e){
        clearErrors(profileForm);
        let name  = document.getElementById('name').value.trim();
        let email = document.getElementById('email').value.trim();
        let pic   = document.getElementById('profile_picture');

        if(name === ''){ e.preventDefault(); showError(profileForm, 'Name is required.'); return; }
        if(!isValidEmail(email)){ e.preventDefault(); showError(profileForm, 'Please enter a valid email.'); return; }

        if(pic && pic.files && pic.files.length > 0){
            let f = pic.files[0];
            if(!['image/jpeg', 'image/png'].includes(f.type)){
                e.preventDefault(); showError(profileForm, 'Picture must be JPEG or PNG.'); return;
            }
            if(f.size > 2 * 1024 * 1024){
                e.preventDefault(); showError(profileForm, 'Picture must be 2 MB or less.'); return;
            }
        }
    });
}

// ---------- password form -----------------------------------------------

let passwordForm = document.getElementById('passwordForm');
if(passwordForm){
    passwordForm.addEventListener('submit', function(e){
        clearErrors(passwordForm);
        let cur  = document.getElementById('current_password').value;
        let n1   = document.getElementById('new_password').value;
        let n2   = document.getElementById('new_password2').value;
        if(cur === ''){ e.preventDefault(); showError(passwordForm, 'Current password is required.'); return; }
        if(n1.length < 8){ e.preventDefault(); showError(passwordForm, 'New password must be at least 8 characters.'); return; }
        if(n1 !== n2){ e.preventDefault(); showError(passwordForm, 'New passwords do not match.'); return; }
    });
}
