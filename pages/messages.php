<?php

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Message.php';
require_once 'utils/redirect.php';

$user_to_id;

if (isset($_GET['u']) && $_GET['u'] != '') {
  $user_to_id = $_GET['u'];
} else {
  include '404.php';
  
  return;
}

$database = new Database();
$db = $database->connect();

$user = new User($db);

$message = new Message($db, $user);

$user_from_id;

if (isset($_SESSION['user'])) {
  $user_from_id = $_SESSION['user']['id'];
} else {
  return redirect('/login');
}

$conversation = $message->get_conversation($user_from_id, $user_to_id);

?>

<ul id="chat-container" class="flex flex-col mt-20 px-20">
  <li></li>
</ul>

<script type="module">
const chatContainer = document.querySelector('#chat-container')

const userToId = <?= $user_to_id ?>;
const userFromId = <?= $_SESSION['user']['id'] ?>

let lastConversation = {}

const getConversation = async () => {
  const res = await fetch('/api/messages/get_conversation.php', {
    method: 'POST',
    body: JSON.stringify({
      userOneId: userFromId,
      userTwoId: userToId
    })
  })

  const {
    message,
    conversation,
    success = false
  } = await res.json()



  if (success) {
    if (JSON.stringify(conversation) !== JSON.stringify(lastConversation)) {
      lastConversation = conversation
    } else {
      return
    }

    chatContainer.innerHTML = ''

    conversation.forEach(message => {
      const li = document.createElement('li')

      li.innerText = message.content

      if (message.user_from_id !== userFromId) {
        li.classList.add('self-end')
      }

      chatContainer.appendChild(li)

    })
  } else {
    console.log(message)
  }
}

setInterval(() => {
  getConversation()
}, 1000)
</script>