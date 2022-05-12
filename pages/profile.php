<?php

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Post.php';
require_once 'utils/format_date.php';
require_once 'utils/redirect.php';

$user_id;

if (isset($_GET['u']) && $_GET['u'] != '') {
  $user_id = $_GET['u'];
} else {
  include '404.php';
  
  return;
}

$database = new Database();
$db = $database->connect();

$user = new User($db); 
$post = new Post($db, $user); 

['user' => $user_data, 'success' => $success] = $user->get_single($user_id);

if (!$success) {
  include '404.php';

  return;
}

$followings_count = count($user->get_followings($user_id));
$followers_count = count($user->get_followers($user_id));

['posts' => $posts, 'author' => $author] = $post->get_all_from_user($user_id);

$posts = array_reverse($posts);

$is_owner = false;

$is_logged = false;

$is_following = false;

if (isset($_SESSION['user'])) {
  $is_logged = true;
  $is_following = $user->is_following($_SESSION['user']['id'], $user_id);
  
  if ($_SESSION['user']['id'] == $user_id) {
    $is_owner = true;
  }
}

?>

<div class="w-full min-h-full py-20 bg-blue-100 flex justify-center items-center">
  <div class="w-2/3 min-w-[512px]">
    <a href="/">back to home</a>
    <a href="/api/users/logout.php">log out</a>
    <header class="w-full bg-white rounded-lg">
      <div class="h-60 rounded-lg bg-blue-500"></div>
      <div class="flex px-10 pt-4">
        <div class="-mt-16">
          <div class="w-28 h-28 border-solid border-4 border-white bg-black rounded-full"></div>
        </div>
        <div class="max-w-xl ml-4 pb-8">
          <h3 class="text-xl"><?= $user_data['firstname'] ?> <?= $user_data['lastname'] ?></h3>
          <div>
            <div class="flex gap-5 mt-2">
              <h4 class="text-gray-400 text-sm"><span class="text-black"><?= $followings_count ?></span>
                followings
              </h4>
              <h4 class="text-gray-400 text-sm"><span class="text-black"><?= $followers_count ?></span> followers</h4>
            </div>
            <p class="mt-4 text-base">
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat inventore velit libero temporibus. Quidem
              illo exercitationem ratione atque impedit repudiandae, odio aut ab nisi vero eos blanditiis rem iste quod
              debitis officiis commodi nesciunt quis cupiditate, qui tempore beatae sapiente, vitae ipsam! Dignissimos
              ipsa libero iusto, velit aut ab quidem!
            </p>
          </div>
        </div>
        <div class="flex justify-end flex-1">
          <?php if ($is_owner): ?>
          <div>edit</div>
          <?php elseif ($is_logged && !$is_following): ?>
          <button id="profile-follow-button" class="button self-start">follow</button>
          <?php else: ?>
          <button id="profile-unfollow-button" class="button self-start">unfollow</button>
          <?php endif ?>
        </div>
      </div>
    </header>

    <div class="flex gap-10 mt-10">
      <div class="flex flex-col gap-10 flex-1">
        <?php foreach ($posts as $post): ?>
        <div class="bg-white p-5 rounded-lg">
          <header class="flex gap-5">
            <div>
              <div class="w-12 h-12 bg-black rounded-full"></div>
            </div>
            <div>
              <h3 class="text-lg"><?= $author['firstname'] ?> <?= $author['lastname'] ?></h3>
              <h4 class="text-gray-400 text-sm"><?= format_date($post['published_at']) ?></h4>
            </div>
            <div class="flex-1 flex justify-end">
              <div>dots</div>
            </div>
          </header>
          <p class="mt-5 text-base">
            <?= $post['content'] ?>
          </p>
        </div>
        <?php endforeach ?>
      </div>

      <aside class="w-96 bg-white rounded-lg max-h-96"></aside>
    </div>
  </div>
</div>

<script type="module">
import redirect from '../utils/redirect.js'

const profileFollowButton = document.querySelector('#profile-follow-button')
const profileUnfollowButton = document.querySelector('#profile-unfollow-button')

const followingId = <?= $user_id ?>;
const userId = <?= $_SESSION['user']['id'] ?>

if (profileFollowButton) {
  profileFollowButton.addEventListener('click', () => {

    const handleFollow = async () => {
      const res = await fetch('/api/users/follow.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          followingId,
          userId
        })
      })

      const {
        message,
        success = false
      } = await res.json()

      if (success) {
        redirect(`/profile?u=${followingId}`)
      }
    }

    handleFollow()
  })
} else if (profileUnfollowButton) {
  profileUnfollowButton.addEventListener('click', () => {

    const handleUnfollow = async () => {
      const res = await fetch('/api/users/unfollow.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          followingId,
          userId
        })
      })

      const {
        message,
        success = false
      } = await res.json()

      if (success) {
        redirect(`/profile?u=${followingId}`)
      }

    }

    handleUnfollow()
  })
}
</script>