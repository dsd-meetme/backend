<?php
system("./git.sh -i .git_utility/key.key git fetch");
system("./git.sh -i .git_utility/key.key git pull origin master");
system('composer update');
?>
