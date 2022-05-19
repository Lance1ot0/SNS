<?php

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'utils/redirect.php';

$user_data;

if (!isset($_SESSION['user'])) {
  // redirect('/login');
} else {
  $user_data = $_SESSION['user'];
}

$database = new Database();
$db = $database->connect();

$user = new User($db); 

$users = $user->get_all();
$users = json_decode($users);
$data_users = array();

for ($i = 0; $i < count($users); $i++){
  $data_users[] = array(
    "id" => $users[$i]->id,
    "firstname" => $users[$i]->firstname,
    "lastname" => $users[$i]->lastname
  );
}

$data_users = json_encode($data_users);

file_put_contents('allUsers.json', $data_users);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../dist/global.css">
  <title>SNS</title>
</head>

<body>
  <?php if (isset($_SESSION['user'])): ?>
  <nav class="z-50 hidden h-20 w-full bg-white fixed top-0 lg:flex place-items-center justify-between">

    <a href="/">
      <img src="/images/shared.svg" alt="" class="ml-24">
    </a>

    <div class="max-w-md">
      <form action="" method="POST">
        
        <input type="text"
          class="px-4 py-2 border-2 transition-[border-color] duration-300 focus:border-blue-500 border-blue-300 outline-none rounded-md text-lg placeholder:text-blue-300 text-blue-500"
          placeholder="Search"
          name="searchUsers" 
          id="searchUsers"
          autocomplete="off"
           />

          </form>
          <div class="match-list"></div>
    </div>

    <div class="mr-24 text-lg">
      <ul class="flex gap-10">
        <li>
          <a href="/" class="flex gap-3"><img src="../images/home.svg" alt="" /> Home</a>
        </li>
        <li>
          <a href="/profile?u=<?= $user_data['id'] ?>" class="flex gap-3"><img src="../images/relation.svg" alt="" />
            Account</a>
        </li>
        <li>
          <a href="/pages" class="flex gap-3"><img src="../images/page.svg" alt="pages" /> Pages</a>
        </li>
        <li>
          <a href="/groups" class="flex gap-3"><img src="../images/group.svg" alt="groups" /> Groups</a>
        </li>
        <li>
          <a href="/messages" class="flex gap-3"><img src="../images/message.svg" alt="messages" /> Messages</a>
        </li>
        <li><a href="/api/users/logout.php" class="button">log out</a></li>
      </ul>
    </div>
  </nav>
  <script>
    const search = document.querySelector('#searchUsers');

    const matchList = document.querySelector('.match-list');



    const searchStates = async searchText => {
      const res = await fetch('allUsers.json');
      const states = await res.json();

      const matches = states.filter(state => {
          const regex = new RegExp(`^${searchText}`, 'gi');
          
          return toString(state.id).match(regex) || state.firstname.match(regex) || state.lastname.match(regex);
      });


      if (searchText === 0) {
          matches = [];
          

      };
      outputHtml(matches);

    };

    
    const outputHtml = matches => {

      if (search.value === ""){
        matchList.innerHTML = ""
      }else if (matches.length > 0) {
          const html = matches.map(match => `
              <ul> 
                <li>  
                  <a href="/profile?u=${match.id}" class="linkPage"> ${match.firstname} ${match.lastname} </a> 
                </li>
              </ul>
              `).join('');

          matchList.innerHTML = html;
      } else {
          matchList.innerHTML = '<p class="noMatch">Aucune Correspondance</p>'
      }
    };

    search.addEventListener('input', () => searchStates(search.value))
  </script>
  <?php endif ?>

