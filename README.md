# Sentinela


# Resumo

1- Sentinelas são aplicadas quando por algum motivo, determinada situação deve ser monitorada;<br>
2- A funcionalidade básica é para buscar informações específicas, tratar e enviar por e-email aos Gestores ou demais colaboradores citados;<br>
3- Todas as sentinelas são documentadas no Git, a fim de manter os padrões organizados.

#Servidor alocado:
192.168.240.51

#Diretório raiz:
C:\xampp\htdocs\sentinela


#Funcionalidades

1- Estrutura das pastas:<br>
 a- Setor<br>
 b- Sentinela<br>
  b.1- dados.php : Página que faz as buscas no banco e trata os valores;<br>
  b.2- email.php : Página que recebe informações de (dados.php) e dispara o e-mail;<br>
  b.3- index.php : Configuração de quem vai receber e-mails.<br>
  
2- Disparo de e-mails:<br>
  a- Os disparos são configurados pelo agendador de tarefas do windows no mesmo servidor em que aplicação encontra-se alocada.<br>
  b- Os horários de cada tarefa devem ser definidas com o solicitante.
  
