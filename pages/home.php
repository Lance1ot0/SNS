<?php 

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Post.php';
require_once 'utils/format_date.php';
require_once 'utils/redirect.php';
require_once 'utils/is_emoji.php';

$user_data;

if (!isset($_SESSION['user'])) {
  redirect('/login');
} else {
  $user_data = $_SESSION['user'];
}

$database = new Database();
$db = $database->connect();

$user = new User($db); 
$post = new Post($db, $user); 

$posts = $post->get_all();

$posts = array_reverse($posts);

?>

<div class="w-full py-20 min-h-full bg-blue-100 flex flex-col justify-center items-center">
  <h1 class="text-6xl font-bold">SNS</h1>
  <a href="/profile?u=<?= $user_data['id'] ?>">go to my profile</a>
  <div class="mt-10 bg-white rounded-lg p-5 w-1/2 min-w-[512px] flex flex-col items-center">
    <form id="create-post-form" action="/api/posts/create.php" method="POST" class="w-full flex flex-col gap-2">
      <div class="flex gap-2">
        <div>
          <a href="/profile?u=<?= $user_data['id'] ?>" class="group cursor-pointer">
            <div style="background-image: url(<?= $user->get_profile_picture($user_data['id']) ?>);"
              class="group-hover:border-blue-500 bg-cover border-transparent border-solid border-2 transition-[border-color] duration-300 w-12 h-12 bg-gray-400 rounded-full">
            </div>
          </a>
        </div>
        <textarea name="content" type="text" placeholder="Content" class="input w-full"></textarea>
      </div>
      <span id="create-post-form-message"></span>
      <button class="button flex self-end justify-center items-center gap-4">
        <div id="create-post-form-spin-container" class="hidden">
          <?php
            include 'shared/spin.php';
          ?>
        </div>
        Publish
      </button>
    </form>
  </div>
  <div class="mt-10 flex flex-col gap-10 flex-1 w-1/2 min-w-[512px]">
    <?php foreach ($posts as $post): ?>
    <?php
      $content = $post['post']['content'];

      $content_is_an_emoji = is_emoji($content) && grapheme_strlen($content) == 1;      
    ?>
    <div id="post-<?= $post['post']['id'] ?>" class="bg-white p-5 rounded-lg">
      <header class="flex gap-5">
        <a href="/profile?u=<?= $post['author']['id'] ?>" class="group cursor-pointer">
          <div style="background-image: url(<?= $user->get_profile_picture($post['author']['id']) ?>);"
            class="group-hover:border-blue-500 bg-cover border-transparent border-solid border-2 transition-[border-color] duration-300 w-12 h-12 bg-gray-400 rounded-full">
          </div>
        </a>
        <div>
          <h3 class="text-lg"><?= $post['author']['firstname'] ?> <?= $post['author']['lastname'] ?></h3>
          <h4 class="text-gray-400 text-sm"><?= format_date($post['post']['published_at']) ?></h4>
        </div>
        <div class="flex-1 flex justify-end">
          <?php 
            if ($post['author']['id'] == $_SESSION['user']['id']):
          ?>
          <div class="relative cursor-pointer">
            <span class="dots"><img src="/images/more.svg" alt=""></span>
            <ul class="mt-2 hidden absolute left-0 rounded-lg  overflow-hidden bg-white shadow-sm shadow-slate-300">
              <li
                class="delete-button px-4 items-center flex gap-2 py-2 hover:bg-gray-50 transition-[background-color] duration-300">
                <div class="scale-75 hidden">
                  <?php
                    include 'shared/spin.php';
                  ?>
                </div>
                <button class="disabled:text-gray-200">delete</button>
              </li>
            </ul>
          </div>
          <?php endif ?>
        </div>
      </header>
      <p class="break-all mt-5 text-base">
        <?php if ($content_is_an_emoji): ?>
        <span class="text-5xl">
          <?= $post['post']['content'] ?>
        </span>
        <?php else: ?>
        <?= $post['post']['content'] ?>
        <?php endif ?>
      </p>
    </div>
    <?php endforeach ?>
  </div>
</div>

<script type="module">
import getFormData from '../utils/getFormData.js'
import redirect from '../utils/redirect.js'

const createPostFormElement = document.querySelector('#create-post-form')

const createPostFormSpinContainerElement = document.querySelector('#create-post-form-spin-container')

const dotsElements = [...document.querySelectorAll('.dots')]

dotsElements.forEach(dotsElement => {
  let isDotsOpened = false

  dotsElement.addEventListener('click', e => {
    const dotsMenu = dotsElement.nextElementSibling

    if (isDotsOpened) {
      dotsMenu.classList.add('hidden')
    } else {
      dotsMenu.classList.remove('hidden')
    }

    isDotsOpened = !isDotsOpened
    const postId = e.target.parentElement.parentElement.parentElement.parentElement.id.split('-')[1]

    const deleteButton = dotsMenu.querySelector('.delete-button')

    const handleDelete = async () => {
      deleteButton.firstElementChild.classList.remove('hidden')
      deleteButton.lastElementChild.disabled = true

      const res = await fetch('/api/posts/delete.php', {
        method: 'DELETE',
        body: JSON.stringify({
          id: postId
        })
      })

      const {
        message,
        success
      } = await res.json()

      deleteButton.firstElementChild.classList.add('hidden')
      deleteButton.lastElementChild.disabled = false

      if (success) {
        redirect('/')
      }
    }

    deleteButton.addEventListener('click', handleDelete)

  })
})

createPostFormElement.addEventListener('submit', e => {
  e.preventDefault()

  const createPostFormMessageElement = document.querySelector('#create-post-form-message')

  const {
    content
  } = getFormData(createPostFormElement)

  if (!content) {
    createPostFormMessageElement.innerText = 'Please, add some content to your post.'
    createPostFormMessageElement.className = 'error-message'

    return
  }

  const handleCreatePost = async () => {
    createPostFormSpinContainerElement.className = ''
    createPostFormSpinContainerElement.parentElement.disabled = true

    const res = await fetch('/api/posts/create.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        content,
      })
    })

    const {
      message,
      success = false
    } = await res.json()

    createPostFormSpinContainerElement.className = 'hidden'
    createPostFormSpinContainerElement.parentElement.disabled = false

    createPostFormMessageElement.innerText = message

    if (success) {
      createPostFormMessageElement.className = 'success-message'
      redirect('/')
    } else {
      createPostFormMessageElement.className = 'error-message'
    }
  }

  handleCreatePost()
})
</script>