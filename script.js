// Função para mostrar seções do menu
function showSection(sectionId) {
    // Esconder todas as seções
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });
    
    // Mostrar a seção selecionada
    const targetSection = document.getElementById(sectionId);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Atualizar menu ativo
    const menuItems = document.querySelectorAll('#sidebar .components li');
    menuItems.forEach(item => {
        item.classList.remove('active');
    });
    
    // Encontrar e ativar o item do menu correspondente
    const activeMenuItem = document.querySelector(`#sidebar .components li a[onclick="showSection('${sectionId}')"]`);
    if (activeMenuItem) {
        activeMenuItem.parentElement.classList.add('active');
    }
    
    // Fechar sidebar no mobile após seleção
    if (window.innerWidth <= 768) {
        toggleSidebar();
    }
}

// Função para copiar o código PIX
function copyPixCode() {
    const pixCodeInput = document.getElementById('pixCodeInput');
    const copyBtn = document.querySelector('.copy-btn');
    
    if (pixCodeInput) {
        // Selecionar e copiar o texto
        pixCodeInput.select();
        pixCodeInput.setSelectionRange(0, 99999); // Para mobile
        
        // Usar a API moderna de clipboard se disponível
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(pixCodeInput.value).then(() => {
                showCopyFeedback(copyBtn, 'Código PIX copiado!');
            }).catch(() => {
                // Fallback para método antigo
                document.execCommand('copy');
                showCopyFeedback(copyBtn, 'Código PIX copiado!');
            });
        } else {
            // Fallback para navegadores mais antigos
            document.execCommand('copy');
            showCopyFeedback(copyBtn, 'Código PIX copiado!');
        }
    }
}

// Função para mostrar feedback visual ao copiar
function showCopyFeedback(button, message) {
    const originalText = button.textContent;
    const originalClass = button.className;
    
    // Alterar aparência do botão
    button.textContent = message;
    button.classList.add('copied');
    
    // Restaurar após 2 segundos
    setTimeout(() => {
        button.textContent = originalText;
        button.className = originalClass;
    }, 2000);
}

// Função para toggle do sidebar no mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Toggle do sidebar para mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Fechar sidebar ao clicar fora (mobile)
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        if (window.innerWidth <= 768 && 
            sidebar.classList.contains('active') && 
            !sidebar.contains(event.target) && 
            !sidebarToggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });
    
    // Validação do formulário de pagamento
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(event) {
            const valueInput = document.getElementById('value');
            const value = parseFloat(valueInput.value);
            
            if (value < 0.50) {
                event.preventDefault();
                alert('O valor mínimo para pagamento PIX é R$ 0,50');
                valueInput.focus();
                return false;
            }
            
            // Mostrar loading no botão
            const submitBtn = paymentForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Gerando PIX...';
            submitBtn.disabled = true;
            
            // Restaurar botão se houver erro (timeout de segurança)
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 10000);
        });
    }
    
    // Formatação automática do campo de valor
    const valueInput = document.getElementById('value');
    if (valueInput) {
        valueInput.addEventListener('input', function() {
            let value = this.value.replace(/[^\d.,]/g, '');
            
            // Limitar a 2 casas decimais
            if (value.includes('.')) {
                const parts = value.split('.');
                if (parts[1] && parts[1].length > 2) {
                    value = parts[0] + '.' + parts[1].substring(0, 2);
                }
            }
            
            this.value = value;
        });
        
        // Validação em tempo real
        valueInput.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (value < 0.50 && value > 0) {
                this.style.borderColor = '#ff4757';
                this.style.boxShadow = '0 0 0 3px rgba(255, 71, 87, 0.1)';
            } else {
                this.style.borderColor = '#333';
                this.style.boxShadow = 'none';
            }
        });
    }
    
    // Animação suave para os passos de pagamento
    const steps = document.querySelectorAll('.step');
    steps.forEach((step, index) => {
        step.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Auto-focus no campo de valor quando a página carrega
    if (valueInput && !document.querySelector('.payment-steps')) {
        valueInput.focus();
    }
});

// Função para atualizar o status do pagamento (para uso futuro com webhook)
function updatePaymentStatus(status) {
    const statusElement = document.querySelector('.payment-status');
    if (statusElement) {
        statusElement.textContent = status;
        statusElement.className = `payment-status ${status.toLowerCase()}`;
    }
}

// Função para recarregar a página (botão "Já fiz o pagamento")
function resetPayment() {
    if (confirm('Tem certeza que deseja iniciar um novo pagamento?')) {
        window.location.reload();
    }
}

// Função para detectar mudanças de orientação no mobile
window.addEventListener('orientationchange', function() {
    setTimeout(() => {
        // Reajustar layout se necessário
        const qrCode = document.querySelector('.qr-code');
        if (qrCode && window.innerWidth <= 768) {
            if (window.orientation === 90 || window.orientation === -90) {
                qrCode.style.width = '200px';
                qrCode.style.height = '200px';
            } else {
                qrCode.style.width = '250px';
                qrCode.style.height = '250px';
            }
        }
    }, 100);
});

// Função para debug (remover em produção)
function debugInfo() {
    console.log('Pushin Pay - Sistema PIX carregado');
    console.log('Viewport:', window.innerWidth + 'x' + window.innerHeight);
    console.log('User Agent:', navigator.userAgent);
}

// Executar debug apenas em desenvolvimento
if (window.location.hostname === 'localhost' || window.location.hostname.includes('manusvm')) {
    debugInfo();
}

