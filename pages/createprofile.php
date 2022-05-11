<?php

include '../partials/header.php';

?>

<div class="w-full h-full bg-blue-100 flex flex-col justify-center items-center">
    <div class="bg-white rounded-lg p-12 w-1/3 min-w-[512px] flex flex-col items-center">
        <h2 class="text-blue-500 text-4xl">Choose profile picture</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="file">
                <input type="file" id="files">
            </label>
            
            
        </form>
    </div>
</div>

<?php

include '../partials/footer.php'

?>