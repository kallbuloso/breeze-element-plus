<?php

return [
    'common' => [
        'greeting' => 'Olá, :name!',
        'expiration' => '{1} Este link expira em :count minuto.|[2,*] Este link expira em :count minutos.',
        'fallback' => 'Se o botão “:action” não funcionar, copie e cole o endereço abaixo no navegador:',
        'rights' => 'Todos os direitos reservados.',
    ],
    'verification' => [
        'subject' => 'Confirme seu endereço de e-mail',
        'preview' => 'Confirme seu e-mail para começar a usar o :app.',
        'heading' => 'Confirme seu e-mail',
        'intro' => 'Obrigado por criar sua conta no :app. Confirme seu endereço de e-mail para concluir seu cadastro.',
        'action' => 'Confirmar e-mail',
        'outro' => 'Se você não criou esta conta, ignore este e-mail com segurança.',
    ],
    'password_reset' => [
        'subject' => 'Redefina sua senha',
        'preview' => 'Recebemos uma solicitação para redefinir sua senha no :app.',
        'heading' => 'Redefina sua senha',
        'intro' => 'Recebemos uma solicitação para redefinir a senha da sua conta no :app.',
        'action' => 'Redefinir senha',
        'outro' => 'Se você não solicitou uma nova senha, ignore este e-mail com segurança.',
    ],
];
