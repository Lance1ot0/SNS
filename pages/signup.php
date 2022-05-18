<div class="w-full h-full bg-blue-100 flex flex-col justify-center items-center">
  <div class="bg-white rounded-lg p-12 w-1/3 min-w-[512px] flex flex-col items-center">
    <h2 class="text-blue-500 text-4xl">Welcome</h2>
    <form id="signup-form" action="/api/users/create.php" method="POST" class="w-full flex flex-col gap-5 mt-10">
      <input name="firstname" type="text" placeholder="First name" class="input w-full">
      <input name="lastname" type="text" placeholder="Last name" class="input w-full">
      <input name="email" type="email" placeholder="Email" class="input w-full">
      <div class="flex items-center w-full relative">
        <input name="password" type="password" placeholder="Password" class="input w-full">
        <button id="toggle-password-visibility" class="absolute right-0 mr-5 cursor-pointer">
          <img src="../images/password-hide.svg" alt="password visibility">
        </button>
      </div>
      <span id="signup-form-message"></span>
      <button type="submit" class="button w-full flex justify-center items-center gap-4">
        <div id="signup-form-spin-container" class="hidden">
          <?php
            include 'shared/spin.php';
          ?>
        </div>
        Sign up
      </button>
    </form>
  </div>
  <p class="mt-10">Already have an account ? <a href="/login" class="text-blue-500 hover:underline">Log in</a></p>
</div>

<script type="module">
import getFormData from '../utils/getFormData.js'
import redirect from '../utils/redirect.js'

const togglePasswordVisibilityButton = document.querySelector('#toggle-password-visibility')
const signupForm = document.querySelector('#signup-form')

const signupFormSpinContainer = document.querySelector('#signup-form-spin-container')

let isVisible = false

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

signupForm.addEventListener('submit', e => {
  e.preventDefault()

  const signupFormMessage = document.querySelector('#signup-form-message')

  const {
    firstname,
    lastname,
    email,
    password
  } = getFormData(signupForm)

  if (!firstname || !lastname || !email || !password) {
    signupFormMessage.innerText = 'Please, fill all the fields.'
    signupFormMessage.className = 'error-message'

    return
  }

  const handleSignup = async () => {
    signupFormSpinContainer.className = ''
    signupFormSpinContainer.parentElement.disabled = true

    const res = await fetch('/api/users/create.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        firstname,
        lastname,
        email,
        password,
      })
    })

    const {
      message,
      success = false
    } = await res.json()

    signupFormSpinContainer.className = 'hidden'
    signupFormSpinContainer.parentElement.disabled = false

    signupFormMessage.innerText = message

    if (success) {
      signupFormMessage.className = 'success-message'
      redirect('/')
    } else {
      signupFormMessage.className = 'error-message'
    }
  }

  handleSignup()
})
</script>