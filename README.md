# Smaregi Authentication

1. リポジトリのクローン作成
2. .env.example fileを.envにコピーする
3. `SMAREGI_CLIENT_ID`に`SMAREGI_CLIENT_SECRET`に、スマレジのアプリ登録時に発行されたキーを入力
    ```
    SMAREGI_CLIENT_ID=
    SMAREGI_CLIENT_SECRET=
    SMAREGI_REDIRECT_URL=/smaregi/auth/callback
    ```
4. Composerをインストールする
5. データベースをセットアップする
   ```
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=
    DB_PASSWORD=
    ```
6. データベースマイグレーションを行う (`php artisan migrate`)
7. アプリケーションをサーブする (`php artisan serve`)
8. ローカルでhttp://127.0.0.1:8080にアクセスする
