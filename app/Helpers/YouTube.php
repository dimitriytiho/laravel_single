<?php


namespace App\Helpers;


class YouTube
{
    public $apiKey;
    public $channelId;


    private function __construct()
    {
        $this->apiKey = config('add.youtube_api_key');
        $this->channelId = config('add.youtube_channel_id');
    }


    /*

    // Пример использования с кэшированием
    $time = 86400; // Сутки
    if (cache()->has('you_tube_video')) {
        $youTubeVideo = cache()->get('you_tube_video');
    } else {
        $youTubeVideo = YouTube::getLastVideo(4, true);
        cache()->put('you_tube_video', $youTubeVideo, $time);
    }

     */


    /**
     *
     * @return array
     *
     * Возвращает массив с последними видео с Ютуб канала.
     * В массиве будет: videoId, title, description.
     * $maxResults - кол-во последних видео, по-умолчанию 4, необязательный паратетр.
     * $statistics - если передать true, то в массив добавиться: like, dislike, по-умолчанию false, необязательный паратетр.
     * Рекомендуемся кэшировать результат, чтобы каждый раз не делать запрос к YouTube API.
     */
    public static function getLastVideo($maxResults = 4, $statistics = false)
    {
        $arr = [];
        $ids = '';
        $self = new self();
        if ($maxResults && $self->apiKey && $self->channelId) {

            $url = "https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId={$self->channelId}&maxResults={$maxResults}&key={$self->apiKey}";
            $list = json_decode(file_get_contents($url));

            if (!empty($list->items)) {
                foreach ($list->items as $key => $item) {

                    // Формируем нужный массив
                    $arr[$key]['videoId'] = $item->id->videoId ?? null;
                    $arr[$key]['title'] = $item->snippet->title ?? null;
                    $arr[$key]['description'] = $item->snippet->description ?? null;

                    // Записываем в строку id, для которых нужна статистика
                    if ($statistics && !empty($item->id->videoId)) {
                        $ids .= "id={$item->id->videoId}&";
                    }
                }

                // Если нужна статистика
                if ($statistics && $ids) {
                    $ids = rtrim($ids, '&');
                    $statistic = self::getStatistics($ids);

                    if (!empty($statistic->items)) {
                        foreach ($statistic->items as $key => $item) {

                            // Добавляем в массив статистику
                            $arr[$key]['like'] = $item->statistics->likeCount ?? '0';
                            $arr[$key]['dislike'] = $item->statistics->dislikeCount ?? '0';
                        }
                    }
                }
            }
        }
        return $arr;
    }


    /**
     *
     * @return object
     *
     * Возвращаем объект с данные о видео с Ютуб, такие как лайки, дислайки и пр.
     * $ids - передать в формате id=qSCNvJIM54Q&id=HIVanW57wII (несколько как здесь или один).
     */
    public static function getStatistics($ids)
    {
        $self = new self();
        if ($self->apiKey && $self->channelId && $ids) {
            $url = "https://www.googleapis.com/youtube/v3/videos?part=statistics&{$ids}&key={$self->apiKey}";
            return json_decode(file_get_contents($url));
        }
        return false;
    }
}
