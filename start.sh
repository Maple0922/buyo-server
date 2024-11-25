#!/bin/bash

# データベースファイルが存在しない場合は作成
if [ ! -f /var/lib/litefs/data/database.sqlite ]; then
    touch /var/lib/litefs/data/database.sqlite
    chown www-data:www-data /var/lib/litefs/data/database.sqlite
fi

# マイグレーションの実行
php artisan migrate --force

# LiteFSとApacheの起動
litefs mount & apache2-foreground
