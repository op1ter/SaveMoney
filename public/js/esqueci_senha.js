function handleRecovery(event) {
            event.preventDefault();
            const emailInput = document.getElementById('email-recovery');
            const statusMsg = document.getElementById('status-msg');

            if (emailInput.value.trim() === '') {
                emailInput.classList.add('invalid');
                document.getElementById('email-error').style.display = 'block';
            } else {
                emailInput.classList.remove('invalid');
                document.getElementById('email-error').style.display = 'none';
                
                // 1. Mostra a mensagem de carregamento
                statusMsg.style.color = "#333";
                statusMsg.innerText = "Enviando e-mail, aguarde...";
                statusMsg.style.display = 'block';

                // 2. Faz a requisição para o PHP
                fetch('../backend/api/recuperar_senha.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email: emailInput.value })
                })
                .then(response => response.json())
                .then(data => {
                    // 3. Trata a resposta do servidor
                    if (data.status === 'success') {
                        statusMsg.style.color = "#32c54a"; // Fica verde em caso de sucesso
                        statusMsg.innerText = data.message;
                        emailInput.value = ''; // Limpa o campo para o usuário
                    } else {
                        statusMsg.style.color = "#ff4757"; // Fica vermelho em caso de erro
                        statusMsg.innerText = data.message;
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição:", error);
                    statusMsg.style.color = "#ff4757";
                    statusMsg.innerText = "Erro ao comunicar com o servidor. Verifique o console.";
                });
            }
        }

        // Limpa a mensagem ao digitar novamente
        document.getElementById('email-recovery').addEventListener('input', function() {
            this.classList.remove('invalid');
            document.getElementById('email-error').style.display = 'none';
            document.getElementById('status-msg').style.display = 'none';
        });