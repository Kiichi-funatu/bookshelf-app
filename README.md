# Bookshelf App

書籍レビュー・お気に入り・ランキング・検索機能を備えた Laravel アプリケーションです。

---

## 1. プロジェクトの取得

GitHub からソースコードを取得します。

```bash
git clone https://github.com/Kiichi-funatu/bookshelf-app.git
cd bookshelf-app

## 2. Docker のセットアップ

2-1. Docker Desktop のインストール

Docker をインストールしていない場合は以下からインストールしてください。

　https://www.docker.com/get-started/

2-2. コンテナの起動

プロジェクト直下で以下を実行します。

　docker compose up -d --build


PHP コンテナに入ります。

　docker compose exec php bash