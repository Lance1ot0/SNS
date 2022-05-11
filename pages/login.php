<?php

include '../partials/header.php'

?>

<div class="w-full h-full bg-blue-100 flex justify-center items-center">
  <div class="bg-white rounded-lg p-12 w-1/3 min-w-[512px] flex flex-col items-center">
    <h2 class="text-blue-500 text-4xl">Welcome back</h2>
    <form id="login-form" action="/login" method="POST" class="w-full flex flex-col gap-5 mt-10">
      <input type="email" placeholder="Email" class="input w-full">
      <div class="flex items-center w-full relative">
        <input type="password" placeholder="Password" class="input w-full">
        <div id="toggle-password-visibility" class="absolute right-0 mr-5 cursor-pointer">
          <img src="../images/password-hide.svg" alt="password visibility">
        </div>
      </div>
      <button class="button w-full">Log in</button>
    </form>
  </div>
</div>

<script>
const togglePasswordVisibilityElement = document.querySelector('#toggle-password-visibility')
const formElement = document.querySelector('#login-form')

let isVisible = false

const togglePasswordVisible = () => {
  let path = '../images/password-hide.svg'
  
  if (isVisible) {
    path = '../images/password-show.svg'
  }
  
  togglePasswordVisibilityElement.firstElementChild.src = path

  togglePasswordVisibilityElement.previousElementSibling.type = isVisible ? 'text' : 'password'
  
  isVisible = !isVisible
}

togglePasswordVisibilityElement.addEventListener('click', togglePasswordVisible)

formElement.addEventListener('submit', e => {
  e.preventDefault()
})
</script>

<?php

include '../partials/footer.php'

?>