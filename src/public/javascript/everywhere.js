

window.addEventListener('DOMContentLoaded', function() {
    const signOutButton = document.querySelector('.sign-out-button')
     function logout() {
          window.location.href = '/logout'
     } 
     if (signOutButton) {
          signOutButton.addEventListener('click', logout);
     }

})
