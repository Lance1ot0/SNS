<?php

include '../partials/header.php';

?>

<div class="w-full h-full bg-blue-100 flex flex-col justify-center items-center">
    <div class="bg-white rounded-lg p-12 w-1/3 min-w-[512px] flex flex-col items-center">
        <h2 class="text-blue-500 text-4xl">Welcome</h2>
        <form action="" method="post" class="w-full flex flex-col gap-5 mt-10">
            <input type="text" placeholder="Name" class="input w-full">
            <input type="email" placeholder="Email" class="input w-full">
            <div class="flex items-center w-full relative">
                <input type="password" placeholder="Password" class="input w-full">
                <div id="toggle-password-visibility" class="absolute right-0 mr-5 cursor-pointer">
                    <img src="../images/password-hide.svg" alt="password visibility">
                </div>
            </div>
            <button type="submit" class="button w-full mt-10">Sign Up</button>
        </form>
    </div>
    <p class="mt-10">Already have an account ? <a href="login.php" class="text-blue-500">Log in</a></p>
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