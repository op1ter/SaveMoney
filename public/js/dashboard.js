// Trava de segurança: verifica se o usuário está logado
        window.onload = function() {
            const userName = localStorage.getItem('userName');
            
            if (!userName) {
                // Se não tiver nome salvo, manda pro login
                window.location.href = 'login.html';
            } else {
                // Se tiver, escreve o nome dele na tela (pegando só o primeiro nome)
                const primeiroNome = userName.split(' ')[0];
                document.getElementById('user-name-display').innerText = primeiroNome;
            }
        };

        // Função de Sair
        function logout() {
            localStorage.clear(); // Limpa os dados salvos
            window.location.href = 'login.html'; // Redireciona
        }