<?php

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Message.php';
require_once 'utils/redirect.php';

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

?>

<div class="mt-32 px-24 flex h-3/4">
  <aside class="bg-white p-5 rounded-l-lg w-80">
    <ul id="conversations-container" class="relative h-full w-full">
      <div id="conversations-spin-container"
        class="hidden scale-150 absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
        <?php
          include 'shared/spin.php';
        ?>
      </div>
    </ul>
  </aside>
  <div class="bg-slate-50 rounded-r-lg flex-1 flex flex-col">
    <ul id="chat-container" class="relative gap-4 overflow-scroll flex items-start flex-col p-10 flex-1">
      <div id="chat-spin-container"
        class="hidden absolute scale-150 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
        <?php
          include 'shared/spin.php';
        ?>
      </div>
    </ul>
    <form id="send-message-form" action="/api/messages/create.php" method="POST" class="p-10 flex w-full gap-4">
      <input name="message" placeholder=" Your message" type="text" class="input flex-1">
      <button class="button flex justify-center items-center gap-4">
        <div id="send-button-spin-container" class="hidden">
          <?php
            include 'shared/spin.php';
          ?>
        </div>
        Send
      </button>
    </form>
  </div>
</div>

<script type="module">
import getFormData from '../utils/getFormData.js'

const chatContainer = document.querySelector('#chat-container')
const conversationsContainer = document.querySelector('#conversations-container')
const chatSpinContainer = document.querySelector('#chat-spin-container')
const conversationsSpinContainer = document.querySelector('#conversations-spin-container')
const sendMessageForm = document.querySelector('#send-message-form')
const sendButtonSpinContainer = document.querySelector('#send-button-spin-container')

let userToId
const userFromId = <?= $_SESSION['user']['id'] ?>

let lastConversation = {}

sendMessageForm.addEventListener('submit', async e => {
  e.preventDefault()

  const {
    message
  } = getFormData(sendMessageForm)

  if (message === '') {
    return
  }

  sendButtonSpinContainer.classList.remove('hidden')

  const res = await fetch('/api/messages/send.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      userFromId,
      userToId,
      content: message
    })
  })

  sendButtonSpinContainer.classList.add('hidden')
})

const renderConversations = (users) => {
  conversationsContainer.innerHTML = ''
  chatContainer.innerHTML = `

  <div id="chat-spin-container" class="absolute scale-150 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
    <?php
      include 'shared/spin.php';
    ?>
  </div>
  
  `

  users.forEach(async ({
    user
  }, i) => {
    const url = new URLSearchParams(window.location.search)

    const profilePicture = !user.profile_picture.includes('ui-avatars.com') ?
      `uploads/users/${user.profile_picture}` : user.profile_picture

    const conversationTemplate = `

      <li class="w-full">
        <a href="?u=${user.id}" class="p-5 ${parseInt(url.get('u')) === user.id ? 'bg-slate-50' : ''} flex items-center gap-3 rounded-lg w-full">
          <div>
            <div style="background-image: url(${profilePicture});" class="w-10 h-10 bg-cover bg-gray-400 rounded-full"></div>
          </div>  
          <div class="flex-1">
            <header class="flex items-center gap-8 justify-between w-full">
              <h3 class="font-medium text-lg max-w-[140px] whitespace-nowrap text-ellipsis overflow-hidden">${user.firstname} ${user.lastname}</h3>
              <span class="text-gray-500">1:21</span>
            </header>
            <p class="text-gray-500 text-ellipsis overflow-hidden">Lorem ipsum ut dolor</p>
          </div>
        </a>
      </li>
      
      `
    conversationsContainer.innerHTML += conversationTemplate

  })

  const conversationLinks = conversationsContainer.querySelectorAll('a')

  conversationLinks.forEach(conversationLink => {
    conversationLink.addEventListener('click', e => {
      e.preventDefault()

      const url = new URLSearchParams('?' + e.currentTarget.href.split('?').slice(-1)[0])
      userToId = parseInt(url.get('u'))
      window.history.pushState({}, '', `?u=${userToId}`)

      chatSpinContainer.classList.remove('hidden')

      renderConversations(users)
    })
  })
}

const getUserConversationUsers = async () => {
  conversationsSpinContainer.classList.remove('hidden')

  const res = await fetch('/api/messages/get_user_conversations_users.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      userId: userFromId
    })
  })

  const {
    message,
    users,
    success
  } = await res.json()

  conversationsSpinContainer.classList.add('hidden')

  if (success) {
    if (window.location.search === '') {
      userToId = users[0].user.id
      window.history.pushState({}, '', '?u=' + userToId)
    } else {
      const url = new URLSearchParams(window.location.search)
      userToId = parseInt(url.get('u'))
    }

    chatSpinContainer.classList.remove('hidden')

    getConversation()
    renderConversations(users)
  } else {
    console.log(message)
  }
}

getUserConversationUsers()

const getConversation = async () => {
  const res = await fetch('/api/messages/get_conversation.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      userOneId: userFromId,
      userTwoId: userToId
    })
  })

  const {
    message,
    conversation,
    user_one,
    user_two,
    success = false
  } = await res.json()

  chatSpinContainer.classList.add('hidden')

  if (success) {
    if (JSON.stringify(conversation) !== JSON.stringify(lastConversation)) {
      lastConversation = conversation
    } else {
      return
    }

    chatContainer.innerHTML = ''

    const users = [
      ...[user_one.user],
      ...[user_two.user]
    ]

    conversation.forEach(message => {
      const user = users.find(user => {
        return user.id === message.user_from_id
      })

      const profilePicture = !user.profile_picture.includes('ui-avatars.com') ?
        `uploads/users/${user.profile_picture}` : user.profile_picture

      const messageTemplate = `
      
      <li class="flex gap-4 max-w-md ${message.user_from_id === userFromId ? 'self-end flex-row-reverse' : ''}">
        <div>
          <div style="background-image: url(${profilePicture});" class="bg-cover bg-gray-400 w-10 h-10 rounded-full"></div>
        </div>
        <div class="w-full text-white ${message.user_from_id === userFromId ? 'bg-blue-400' : 'bg-blue-600'} p-5 rounded-lg">
          ${message.content}
        </div>
      </li>

      `

      chatContainer.innerHTML += messageTemplate
    })

    chatContainer.scrollTo(0, chatContainer.scrollHeight);
  } else {
    console.log(message)
  }
}


setInterval(() => {
  if (userToId) getConversation()
}, 2000)
</script>