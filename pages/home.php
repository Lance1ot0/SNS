<?php 

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Post.php';
require_once 'utils/format_date.php';
require_once 'utils/redirect.php';

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
          <div class="w-12 h-12 bg-black rounded-full"></div>
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
    <div class="bg-white p-5 rounded-lg">
      <header class="flex gap-5">
        <a href="/profile?u=<?= $post['author']['id'] ?>" class="group cursor-pointer">
          <div
            class="group-hover:border-blue-500 border-transparent border-solid border-2 transition-[border-color] duration-300 w-12 h-12 bg-black rounded-full">
          </div>
        </a>
        <div>
          <h3 class="text-lg"><?= $post['author']['firstname'] ?> <?= $post['author']['lastname'] ?></h3>
          <h4 class="text-gray-400 text-sm"><?= format_date($post['post']['published_at']) ?></h4>
        </div>
        <div class="flex-1 flex justify-end">
          <div>dots</div>
        </div>
      </header>
      <p class="mt-5 text-base">
        <?= $post['post']['content'] ?>
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