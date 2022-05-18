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
    <nav class="hidden h-20 w-full bg-white lg:flex place-items-center justify-between">

      <img src="/images/Shared.svg" alt="" class="ml-24">

      <div id="input-search" class="max-w-md">
        <input type="text" class="w-full placeholder:text-slate-400 border border-blue-300 rounded-md focus:outline-none focus:border-blue-500 py-2 pl-2 pr-20 shadow-sm" placeholder="Search" />
      </div>

      <div id="navbar_buttons_container" class="mr-24 text-lg">
        <ul id="buttons_container" class="flex gap-10">
          <li>
            <a href="/" class="flex gap-3"><img src="../images/home.svg" alt="" /> Home</a>
          </li>
          <li>
            <a href="/" class="flex gap-3"><img src="../images/relation.svg" alt="" /> Account</a>
          </li>
          <li>
            <a href="/" class="flex gap-3"><img src="../images/page.svg" alt="" /> Pages</a>
          </li>
          <li>
            <a href="/" class="flex gap-3"><img src="../images/group.svg" alt="" /> Groups</a>
          </li>
          <li>
          <a href="/" class="flex gap-3"><img src="../images/message.svg" alt="" /> Messages</a>
          </li>
        </ul>
      </div>
    </nav>
  <?php endif ?>