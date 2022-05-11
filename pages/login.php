<?php

include '../shared/header.php'

?>

<div class="w-full h-full bg-blue-100 flex justify-center items-center">
  <div class="bg-white rounded-lg p-12 w-1/3 min-w-[512px] flex flex-col items-center">
    <h2 class="text-blue-500 text-4xl">Welcome back</h2>
    <form id="login-form" action="/login" method="POST" class="w-full flex flex-col gap-5 mt-10">
      <input name="email" type="email" placeholder="Email" class="input w-full">
      <div class="flex items-center w-full relative">
        <input name="password" type="password" placeholder="Password" class="input w-full">
        <div id="toggle-password-visibility" class="absolute right-0 mr-5 cursor-pointer">
          <img src="../images/password-hide.svg" alt="password visibility">
        </div>
      </div>
      <span id="login-form-message"></span>
      <button class="button w-full flex justify-center items-center gap-4">
        <div id="login-form-spin-container" class="hidden">
          <?php
            include '../shared/spin.php';
          ?>
        </div>
        Log in
      </button>
    </form>
  </div>
</div>

<script type="module">
import getFormData from '../utils/getFormData.js'
import redirect from '../utils/redirect.js'

const togglePasswordVisibilityElement = document.querySelector('#toggle-password-visibility')
const loginFormElement = document.querySelector('#login-form')

const loginFormSpinContainerElement = document.querySelector('#login-form-spin-container')

let isVisible = true

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

loginFormElement.addEventListener('submit', e => {
  e.preventDefault()

  const loginFormMessageElement = document.querySelector('#login-form-message')

  const { email, password } = getFormData(loginFormElement)

  if (!email || !password) {
    loginFormMessageElement.innerText = 'Please, fill all the fields.'
    loginFormMessageElement.className = 'error-message'

    return
  }
  
  const handleLogin = async () => {
    loginFormSpinContainerElement.className = ''
    loginFormSpinContainerElement.parentElement.disabled = true

    const res = await fetch('/api/users/login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        email,
        password
      })
    })

    const { message, success = false } = await res.json()

    loginFormSpinContainerElement.className = 'hidden'
    loginFormSpinContainerElement.parentElement.disabled = false

    loginFormMessageElement.innerText = message

    if (success) {
      loginFormMessageElement.className = 'success-message'
      redirect('/pages/home.php')
    } else {
      loginFormMessageElement.className = 'error-message'
    }
  }

  handleLogin()
})
</script>

<?php

include '../shared/footer.php'

?>