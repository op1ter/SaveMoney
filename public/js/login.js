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
function validateField(inputId, errorId, value) {
            const input = document.getElementById(inputId);
            const errorSpan = document.getElementById(errorId);
            
            if (value.trim() === '') {
                input.classList.add('invalid');
                errorSpan.style.display = 'block';
                return false;
            } else {
                input.classList.remove('invalid');
                errorSpan.style.display = 'none';
                return true;
            }
        }

        function validateLoginForm(event) {
            event.preventDefault(); 

            const email = document.getElementById('email-login').value;
            const password = document.getElementById('password-login').value;
            const globalError = document.getElementById('global-error');

            const isEmailValid = validateField('email-login', 'email-error', email);
            const isPassValid = validateField('password-login', 'password-error', password);

            if (isEmailValid && isPassValid) {
                // Monta os dados para enviar ao PHP
                const loginData = {
                    email: email,
                    senha: password
                };

                // Requisição para o backend
                fetch('../backend/api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(loginData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Salva o nome e ID do usuário no navegador para usar no Dashboard
                        localStorage.setItem('userName', data.user.nome);
                        localStorage.setItem('userId', data.user.id);
                        
                        // Redireciona para o sistema
                        window.location.href = 'dashboard.html';
                    } else {
                        // Exibe o erro retornado pelo banco (ex: E-mail não encontrado, senha incorreta)
                        globalError.innerText = data.message;
                        globalError.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição:", error);
                    globalError.innerText = "Erro ao conectar com o servidor.";
                    globalError.style.display = 'block';
                });
            }
        }

        // Limpa as mensagens de erro ao digitar
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('invalid');
                this.nextElementSibling.style.display = 'none';
                document.getElementById('global-error').style.display = 'none';
            });
        });