#!/bin/bash
# Check if currently in git repository
if [ -d ".git" ]; then
    echo Please move this script outside of the repository before running it!
    exit 1
fi

# Delete leftover files
echo Removing files from earlier runs, please wait...
rm -rf bunny
rm -rf public_html

# Download the contents of the current git repo, and delete git files
git clone --depth=1 https://github.com/jasperweyne/spooky-bunny bunny
rm -rf bunny/.git

# Move bunny/public to public_html/bunny
egrep -lRZ 'public/' bunny | xargs -0 -l sed -i -e 's/public\//..\/public_html\/bunny\//g'
sed -i -e 's/\/config\/bootstrap.php/\/..\/bunny\/config\/bootstrap.php/g' bunny/public/index.php
sed -i -e 's/\"extra\": {/\"extra\": {\n        \"public-dir\": \"..\/public_html\/bunny\",/g' bunny/composer.json
mkdir public_html
mv bunny/public public_html/bunny

# Download/build dependencies
cd bunny
export APP_DEBUG=0 APP_ENV=prod
composer install --no-dev --optimize-autoloader
yarn install
yarn build
cd ../

# Remove files redundant for operation 
echo Removing files redundant for operation, please wait...
rm bunny/* 2> /dev/null
rm -rf bunny/.github
rm -rf bunny/.hooks
rm -rf bunny/assets
rm -rf bunny/bin
rm -rf bunny/node_modules
rm -rf bunny/var

# Create environment variable file
cat > bunny/.env.local.php << EOL
<?php

return array (
    'APP_DEBUG' => '0',
    'APP_ENV' => 'prod',
    'APP_SECRET' => '6badc0fca270ab84a00a67226f9e2554',
    'USERPROVIDER_KEY' => 'ThisIsNotSoSecret',
    'DATABASE_URL' => 'mysql://db_user:db_pass@127.0.0.1:3306/db',
    'MAILER_URL' => 'null://localhost',
    'DEFAULT_FROM' => 'foo@bar.com',
);
EOL
echo Please edit '.env.local.php' and push the code to your server
