document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault()

  const email = document.getElementById('email').value
  const password = document.getElementById('password').value

  const response = await fetch('/backend/api/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email, password })
  })

  const data = await response.json()

  if (data.error) {
    document.getElementById('error').innerText = data.error
  } else {
    // salvar sessão
    localStorage.setItem('token', data.access_token)

    window.location.href = 'dashboard.html'
  }
})