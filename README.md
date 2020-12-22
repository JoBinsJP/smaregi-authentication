# Smaregi Authentication

1. リポジトリのクローン作成
2. .env.example fileを.envにコピーする
3. `SMAREGI_CLIENT_ID`に`SMAREGI_CLIENT_SECRET`に、スマレジのアプリ登録時に発行されたキーを入力
    ```
    SMAREGI_CLIENT_ID=
    SMAREGI_CLIENT_SECRET=
   
   ```
5.`SMAREGI_REDIRECT_URL=`にリダイレクトURL（アプリ概要ページ＞環境設定タブで指定したもの）を入力する
6. Composerをインストールする
7. データベースをセットアップする
   ```
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=
    DB_PASSWORD=
    ```
8. データベースマイグレーションを行う (`php artisan migrate`)
9. アプリケーションをサーブする (`php artisan serve`)
10. ローカルでhttp://127.0.0.1:8080にアクセスする
