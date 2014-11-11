# Welcome to OA's CodeIgniter!

This is OA's framework, It is based on CodeIgniter version 2.1.4!

---
## 聲明
本作品只限分享於研究、研討性質之使用，並不提供任何有營利效益之使用。  
如有營利用途，務必告知作者 OA(<comdan66@gmail.com>)，並且經由作者同意。

<br/>

## 簡介

這是一個以 CodeIgniter 2.1.4 為基礎版本，將其改造出進階功能的 framework!

基本改寫項目如下:

* 匯入並且使用 PHP ActiveRecord ORM
	* 加強 PHP ActiveRecord，新增 recycle、recycle_all、recover 等功能
	* 可以與 OrmImageUploader 搭配結合
<br/><br/>
* 匯入使用 OrmImageUploader 的 library
	* 配合使用 ImageGdUtility、ImageImagickUtility 這兩個主要處理圖片的 library
<br/><br/>
* 匯入使用 cell 的 library
	* 加強有層級結構關係
<br/><br/>
* Identity library 與 identity helper 的使用
<br/><br/>
* Config 的 library 與應用
<br/><br/>
* Cache file folder 的重新定義
<br/><br/>
* Controller core loading 規則順序的改變
<br/><br/>
* 匯入 OA helper 的功能 function
<br/><br/>
* 匯入 可記錄 delay request 的 log 以及 query log
<br/><br/>
* 匯入並且可使用 scss
<br/><br/>
* 匯入並且可使用 compass, gulp.. 等

<br/>

## 快速安裝

* 請先確保您的 server 可以正常啟用 CodeIgniter

* 打開終端機，並且至該 oaci 目錄下，並且貼上以下語法:

	```
cp resource/share/database.php application/config/database.php & mkdir temp & mkdir upload & mkdir application/cell/cache & mkdir application/cache/file & mkdir application/cache/output & touch application/logs/query-log.log & touch application/logs/delay_job-log.log & chmod 777 temp & chmod 777 upload & chmod 777 application/cell/cache & chmod 777 application/cache/file & chmod 777 application/cache/output & chmod 777 application/logs/query-log.log & chmod 777 application/logs/delay_job-log.log & vi application/config/database.php
```
* 最後會開啟 `application/config/database.php`，設定儲存完後即可開始使用！