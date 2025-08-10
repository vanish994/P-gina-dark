# Sistema de Pagamento PIX - Pushin Pay

## Descrição
Sistema completo de pagamento PIX integrado com a API da Pushin Pay, permitindo gerar QR Codes e códigos PIX para pagamentos.

## Funcionalidades Implementadas
- ✅ Geração de QR Code PIX
- ✅ Geração de código PIX (copia e cola)
- ✅ Interface web responsiva
- ✅ Validação de valores
- ✅ Webhook para notificações (estrutura preparada)
- ✅ Sistema de logs

## URLs de Acesso
- **Aplicação Principal**: https://80-ikw68058pigsf3xalvb0i-508520ae.manusvm.computer/index.php
- **Teste da API**: https://80-ikw68058pigsf3xalvb0i-508520ae.manusvm.computer/test_api.php
- **Webhook**: https://80-ikw68058pigsf3xalvb0i-508520ae.manusvm.computer/webhook.php

## Arquivos do Projeto
- `index.php` - Página principal com formulário e geração de PIX
- `style.css` - Estilos da aplicação
- `webhook.php` - Endpoint para receber notificações da Pushin Pay
- `test_api.php` - Página de teste da API

## Como Usar
1. Acesse a URL da aplicação principal
2. Digite o valor desejado para o pagamento (mínimo R$ 0,50)
3. Clique em "Gerar PIX"
4. Use o QR Code ou copie o código PIX para realizar o pagamento

## Status dos Testes
✅ **API da Pushin Pay**: Funcionando corretamente
- Código HTTP: 200
- QR Code gerado com sucesso
- Código PIX válido retornado

✅ **Interface Web**: Funcionando
- Formulário responsivo
- Validação de valores
- Botões de cópia funcionais

## Configurações Técnicas
- **Servidor**: Apache 2.4.52
- **PHP**: 8.1
- **Token API**: Configurado (41909|sPaCf1Ns2mh7K7whnrSjI1Xl48T0lAkfeo2yeZEY772f30ae)
- **Webhook URL**: https://80-ikw68058pigsf3xalvb0i-508520ae.manusvm.computer/webhook.php

## Próximos Passos (Opcionais)
Para um ambiente de produção completo, considere:
1. Configurar banco de dados MySQL para persistir transações
2. Implementar autenticação e autorização
3. Adicionar SSL/TLS próprio
4. Configurar monitoramento e logs avançados
5. Implementar dashboard de administração

## Observações de Segurança
- O token da API está hardcoded no código (adequado para teste, mas deve ser movido para variáveis de ambiente em produção)
- O webhook está preparado mas não salva no banco de dados (estrutura comentada)
- Recomenda-se implementar validação adicional de origem das requisições do webhook

