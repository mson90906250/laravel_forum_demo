<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class TrixImage
{
    private static $inited = false;

    protected static $hashTableName; // 儲存圖片路徑
    protected static $deleteListName; // 儲存等待刪除的cacheKey

    public static function init () {

        if (static::$inited) return;

        static::$hashTableName = env('APP_ENV') === 'testing' ?
            'test_trix_image_hash_table' :
            'trix_image_hash_table';

        static::$deleteListName = env('APP_ENV') === 'testing' ?
            'test_trix_image_delete_list:%s' :
            'trix_image_delete_list:%s';

        static::$inited = true;
    }

    public static function add($filePath)
    {
        $cacheKey = Str::random(20);
        Redis::hset(static::getHashTable(), $cacheKey, $filePath);
        Redis::lpush(static::getDeleteList(), $cacheKey);

        return $cacheKey;
    }

    public static function get($cacheKey)
    {
        return Redis::hget(static::getHashTable(), $cacheKey);
    }

    public static function delete($cacheKey)
    {
        Redis::hdel(static::getHashTable(), $cacheKey);
    }

    public static function persist($persistList)
    {
        // 將image從待刪名單中剔除
        foreach ($persistList as $cacheKey) {
            Redis::hdel(static::getHashTable(), $cacheKey);
        }
    }

    public static function rpop($date = '')
    {
        return Redis::rpop(static::getDeleteList($date));
    }

    public static function exists($cacheKey)
    {
        return boolval(
            Redis::hexists(
                static::getHashTable(),
                $cacheKey
            )
        );
    }

    public static function reset()
    {
        Redis::del(static::getHashTable());
        Redis::del(static::getDeleteList());
    }

    protected static function getHashTable()
    {
        return static::$hashTableName;
    }

    public static function getDeleteList($date = '')
    {
        return sprintf(
            static::$deleteListName,
            $date ?: date('Y-m-d')
        );
    }
}

TrixImage::init();
