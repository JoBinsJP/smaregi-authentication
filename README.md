# Smaregi Authentication

1. リポジトリのクローン作成
2. .env.example fileを.envにコピーする
3. `SMAREGI_CLIENT_ID`に`SMAREGI_CLIENT_SECRET`に、スマレジのアプリ登録時に発行されたキーを入力
    ```
    SMAREGI_CLIENT_ID=
    SMAREGI_CLIENT_SECRET=
   ```
4. `SMAREGI_REDIRECT_URL=`にリダイレクトURL（アプリ概要ページ＞環境設定タブで指定したもの）を入力する
5. Composerをインストールする
6. データベースをセットアップする
   ```
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=
    DB_PASSWORD=
    ```
7. データベースマイグレーションを行う (`php artisan migrate`)
8. アプリケーションをサーブする (`php artisan serve`)
9. ローカルでhttp://127.0.0.1:8080にアクセスする
