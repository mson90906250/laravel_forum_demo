# 關於此 forum demo

<ul>
    <li>因為是個小型的論壇, 且主要是用來展示論壇前端的功能, 所以暫無後台的設定功能</li>
    <li>管理者因為沒有後台做設定的關係, 所以目前將admin寫死在code裡 帳: admin@test.com 密: 123123123</li>
    <li>此論壇demo目前的功能有
        <ol>
            <li>發表文章</li>
            <li>回覆文章</li>
            <li>使用redis來記錄各篇文章的瀏覽人數</li>
            <li>使用<a href="https://github.com/zurb/tribute">Tribute</a>給予的功能, 使使用者可在回覆中以 "@ + username" 來搜尋欲通知的使用者</li>
            <li>使用<a href="https://github.com/basecamp/trix">Trix Editor</a>來達成所見即所得的功能, p.s: 用來給予圖片的敘述(figcaption)似乎其來源有些bug, 所以建議不要使用</li>
            <li>全文搜索的部分使用ElasticSearch加上<a href="https://github.com/babenkoivan">babenkoivan</a>提供的driver來完成</li>
            <li>使用cron, laravel schedule, 及workerman來定時清理沒被使用的圖片</li>
        </ol>
    </li>
</ul>

此demo大約花了1個半月的時間完成, 這期間有部分時間是用來自學vue, laravel, elasticsearch, tdd開發模式和研究一些套件的使用, 所以如果有哪些地方需要改善, 請不吝賜教

## 環境(主要使用docker)

<ul>
    <li>laravel: 7.16.1</li>
    <li>php: 7.3-fpm</li>
    <li>nginx: 1.18</li>
    <li>mysql:5.7.22</li>
    <li>redis:5.0.9</li>
    <li>elasticsearch:7.8.0</li>
</ul>

## 使用方法

<ol>
    <li>clone下來後,到此專案的根目錄下執行"composer install -o"命令</li>
    <li>composer安裝完後, 執行"npm install"命令</li>
    <li>根據需求來調整.env檔案(從.env.example 複製過來)</li>
    <li>設定完, 執行"php artisan key:generate"</li>
    <li>再執行"php artisan migrate" (如果要產生測試資料可在指令後加 "--seed")</li>
    <li>最後一步, 執行"php artisan storage:link"</li>
    <li>如果要使用cron來定時處理沒用到的圖片, 可在cron裡, 加入"* * * * * cd /var/www/laravel-forum && php artisan schedule:run >> /dev/null 2>&1"</li>
</ol>
