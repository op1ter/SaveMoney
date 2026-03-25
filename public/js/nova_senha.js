// Pega o token da URL (ex: ?token=abcd123)
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');

        // Se a pessoa tentar acessar a tela sem um token válido na URL, expulsa pro login
        if (!token) {
            window.location.href = 'login.html';
        }

        function handleNewPassword(event) {
            event.preventDefault();
            const pass = document.getElementById('new-pass').value;
            const confirmPass = document.getElementById('confirm-pass').value;
            const statusMsg = document.getElementById('status-msg');

            if (pass === '' || pass !== confirmPass) {
                document.getElementById('confirm-pass').classList.add('invalid');
                document.getElementById('confirm-error').style.display = 'block';
                return;
            }

            statusMsg.style.color = "#333";
            statusMsg.innerText = "Salvando...";
            statusMsg.style.display = 'block';

            // Envia o Token e a Nova Senha para o PHP
            fetch('../backend/api/atualizar_senha.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: token, nova_senha: pass })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    statusMsg.style.color = "#32c54a";
                    statusMsg.innerText = "Senha alterada com sucesso! Redirecionando...";
                    setTimeout(() => window.location.href = 'login.html', 2000);
                } else {
                    statusMsg.style.color = "#ff4757";
                    statusMsg.innerText = data.message;
                }
            });
        }