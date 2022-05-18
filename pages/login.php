<div class="w-full h-full flex flex-col justify-center items-center">
  <div class="bg-white rounded-lg p-12 w-1/3 min-w-[512px] flex flex-col items-center">
    <h2 class="text-blue-500 text-4xl">Welcome back</h2>
    <form id="login-form" action="/api/users/login.php" method="POST" class="w-full flex flex-col gap-5 mt-10">
      <input name="email" type="email" placeholder="Email" class="input w-full">
      <div class="flex items-center w-full relative">
        <input name="password" type="password" placeholder="Password" class="input w-full">
        <button id="toggle-password-visibility" class="absolute right-0 mr-5 cursor-pointer">
          <img src="../images/password-hide.svg" alt="password visibility">
        </button>
      </div>
      <span id="login-form-message"></span>
      <button class="button w-full flex justify-center items-center gap-4">
        <div id="login-form-spin-container" class="hidden">
          <?php
            include 'shared/spin.php';
          ?>
        </div>
        Log in
      </button>
    </form>
  </div>
  <p class="mt-10">Don't have an account ?<a href="/signup" class="text-blue-500 hover:underline ml-1">Register at</a>
  </p>
</div>

<script type="module">
import getFormData from '../utils/getFormData.js'
import redirect from '../utils/redirect.js'

const togglePasswordVisibilityButton = document.querySelector('#toggle-password-visibility')
const loginForm = document.querySelector('#login-form')

const loginFormSpinContainer = document.querySelector('#login-form-spin-container')

let isVisible = true

const togglePasswordVisibility = () => {
  let path = '../images/password-hide.svg'

  if (isVisible) {
    path = '../images/password-show.svg'
  }

  togglePasswordVisibilityButton.firstElementChild.src = path

  togglePasswordVisibilityButton.previousElementSibling.type = isVisible ? 'text' : 'password'

  isVisible = !isVisible
}

togglePasswordVisibilityButton.addEventListener('click', togglePasswordVisibility)

loginForm.addEventListener('submit', e => {
  e.preventDefault()

  const loginFormMessage = document.querySelector('#login-form-message')

  const {
    email,
    password
  } = getFormData(loginForm)

  if (!email || !password) {
    loginFormMessage.innerText = 'Please, fill all the fields.'
    loginFormMessage.className = 'error-message'

    return
  }

  const handleLogin = async () => {
    loginFormSpinContainer.className = ''
    loginFormSpinContainer.parentElement.disabled = true

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

    const {
      message,
      success = false
    } = await res.json()

    loginFormSpinContainer.className = 'hidden'
    loginFormSpinContainer.parentElement.disabled = false

    loginFormMessage.innerText = message

    if (success) {
      loginFormMessage.className = 'success-message'
      redirect('/')
    } else {
      loginFormMessage.className = 'error-message'
    }
  }

  handleLogin()
})
</script>