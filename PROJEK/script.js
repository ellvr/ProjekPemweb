function validasiForm() {
  var username = document.getElementById('username').value;
  var password = document.getElementById('password').value;
  var errorMessage = document.getElementById('error-message');
  var passwordInput = document.getElementById('password');

  errorMessage.style.display = 'none';
  passwordInput.classList.remove('error-border');

  if (username === '' || password === '') {
    alert('Username dan Password harus diisi!');
    return false;
  }

  if (username !== 'admin' || password !== 'admin123') {
    errorMessage.style.display = 'block';
    passwordInput.classList.add('error-border');
    return false;
  }
  return true;
}