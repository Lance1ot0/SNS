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
      <input type="text"
        class="px-4 py-2 border-2 transition-[border-color] duration-300 focus:border-blue-500 border-blue-300 outline-none rounded-md text-lg placeholder:text-blue-300 text-blue-500"
        placeholder="Search" />
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
      </ul>
    </div>
  </nav>
  <?php endif ?>