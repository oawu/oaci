# Welcome to OA's CodeIgniter!
* 這是一套 [OA Wu](https://www.ioa.tw/) 自家使用的 [PHP](http://php.net/) 框架。
* 它是以 [CodeIgniter](https://www.codeigniter.com/) [v 3.1.6](https://codeload.github.com/bcit-ci/CodeIgniter/zip/3.1.6) 做基礎修改的。

## 功能
* 簡化 CodeIgniter 不常用到的功能。
* 加入使用 [PHP ActiveRecord](http://www.phpactiverecord.org/)。

## 初始化
* 進入 sys 資料夾 `cd sys/cmd`。
* 執行初始化 `php init` 依據指示初始化。

> * 初始化會幫你建立起專案環境，包含 cache、session、log、upload、tmp 資料夾。  
> * 以及在專案下建立一個 `cmd` 路徑指向 `sys/cmd` 以方便日後開發使用。
> * 環境設定會在專案下產生一隻 `_env.php` 的檔案。  
> * 初始化完 database 後，會在 `app/config` 內產生一隻 `database.php` 的檔案。

## MVC 命名
### Controller
* class 與 method 大小寫均不拘，但有區別
* 需配合網址或 Router 大小寫有區分
* 檔名與 class 相同。
* 通常複數命名，視情況而定。

> 例如：書本列表為例 Controller 名稱為 books，網址 https://localhost/books/。

### Model
* class 大駝峰，並且單數命名。
* method 小駝峰。
* 檔名與 class 相同。
* 可以配合指令產生。
* 要使用 Model 時，要先執行 `use_model ()` 初始 Model，預設是關閉的，可以在 `app/config/model.php` 設定 `auto_load` 是否預載 Model。

> 如：資料表名稱 books，則 Model 名稱需為：Book，而不是：Books。

### view 
* 檔名大小寫不拘，可以被讀得到就好。

## Migration
* 設定資料庫連線資料 `app/config/database.php` 確認可以正常資料庫連線。
* 設定檔案在 `app/config/migration.php`，並需要建立在 config 所設定的 Model 檔案。

### 新增
* 至 `cmd/` 執行指令 `php create migration` 即會在設定檔案所指定的路徑下產生 migration 檔案。

### 更新
* 至 `cmd/` 執行指令 `php migration` 即可更新至最新版本。

> 若要升至指定版本，例如：第五版，則 `php migration 5`。
