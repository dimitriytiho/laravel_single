<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Schema;

class Children
{
    /**
     *
     * @return array
     *
     * Возвращает массив из БД, где ключи это id элементов, запрос кэшируется.
     * $model - название модели в формате \App\Page.
     *
     * Если данные не меняются сбросьте кэш!
     */
    public static function getDataKeyId($model)
    {
        // Существует ли модель
        if (class_exists($model)) {
            $method = __FUNCTION__;
            $modelName = class_basename($model);

            // Взязь из кэша
            if (cache()->has($method . $modelName)) {
                return cache()->get($method . $modelName);

            } else {

                // Запрос в БД
                $data =  $model::all()->keyBy('id')->toArray();

                // Кэшируется запрос
                cache()->forever($method . $modelName, $data);
                return $data;
            }
        }
        return [];
    }


    /**
     *
     * @return array
     *
     * Возвращает дерево элементов в БД, если есть вложеннсть.
     * $model - название модели в формате \App\Page.
     * $column - название колонки родителя, по-умолчанию parent_id, необязательный параметр.
     */
    public static function getTree($model, $column = 'parent_id')
    {
        $data = null;
        $tree = [];

        // Существует ли модель
        if (class_exists($model)) {

            // Получаем данные из БД
            $data = self::getDataKeyId($model);
        }
        if ($data) {
            foreach ($data as $idEl => &$node) {

                if ($node[$column] == 0) {
                    $tree[$idEl] = &$node;

                } else {
                    $data[$node[$column]]['children'][$node['id']] = &$node;
                }
            }
        }
        return $tree;
    }


    /**
     *
     * @return array
     *
     * Возвращает массив родителя из дерева.
     * $id = id элемента.
     * $model - название модели в формате \App\Page.
     * $column - название колонки родителя, по-умолчанию parent_id, необязательный параметр.
     */
    public static function getParent($id, $model, $column = 'parent_id')
    {
        if ((int)$id && $model && $column) {
            $data = self::getDataKeyId($model);
            $parentId = $data[(int)$id][$column] ?: '0';
            return self::getFindParent($data, (int)$id, $parentId, $column);
        }
        return [];
    }
    private static function getFindParent($data, $id, $parentId, $column = 'parent_id')
    {
        if ($data && is_array($data)) {
            foreach ($data as $dataId => $value) {
                if ($parentId === '0') {
                    return $data[(int)$id];
                } elseif ($parentId == $dataId) {
                    return self::getFindParent($data, $dataId, (string)$value[$column], $column);
                }
            }
        }
        return [];
    }


    /**
     *
     * @return string или false
     *
     * В тексте ищется текст обрамлённый с 2-х сторон такими символами ###!!, к примеру: ###!!Мета для вывода у вложенных элементов###!!.
     * $text - текст, с этими символами или без них.
     * $symbol - можно изменить искомые символы, необязательный параметр.
     */
    public static function getSymbol($text, $symbol = '###!!')
    {
        if ($text) {
            $pattern = "/{$symbol}([^&]*){$symbol}/";
            //$pattern = "/###!!(.*)###!!/";
            //$text = preg_replace($pattern, '', $text);
            preg_match($pattern, $text, $matches);

            if (!empty($matches[0])) {
                $content = $matches[1];
                $text = str_replace($matches[0], $content, $text);
                // $text Текст весь
                // $content Текст без специальных символов
                return $content;
            }
        }
        return false;
    }



    /**
     *
     * @return string или false
     *
     * Ищет у родителей спец. символы по-умолчанию в description.
     * $parentId - parent_id элемента.
     * $model - название модели в формате \App\Page.
     * $column - название колонки родителя, по-умолчанию parent_id, необязательный параметр.
     * $description - название колонки c тексом, по-умолчанию description, необязательный параметр.
     * $symbol - можно изменить искомые символы, необязательный параметр.
     *
     *
     *
     * ПРИМЕР ИМПОЛЬЗОВАНИЯ
     *
     * Прописать в description элемента:
     * Text ##!!!Мета для вывода у вложенных элементов##!!! text
     *
     * Получить description родителя:
     * $parentText = \App\Helpers\Children::parent(2, '\App\Category');
     *
     */
    public static function parent($parentId, $model, $column = 'parent_id', $description = 'description', $symbol = '##!!!')
    {
        if ((int)$parentId && $model) {
            $data = self::getDataKeyId($model);
            return self::getFindParentId($data, (int)$parentId, $column, $description, $symbol);
        }
        return false;
    }

    private static function getFindParentId($data, $parentId, $column = 'parent_id', $description = 'description', $symbol = '##!!!')
    {
        if ($data && (int)$parentId) {
            foreach ($data as $dataID => $value) {
                if ((int)$parentId == $dataID) {
                    $text = self::getSymbol($value[$description], $symbol);

                    // Если найдём спец. символы, то вернём их
                    if ($text) {
                        return $text;

                    // Иначе рекурсивно запустим этот же метод
                    } else if ((int)$value[$column] != 0) {
                        return self::getFindParentId($data, (int)$value[$column], $column);
                    }
                }
            }
        }
        return false;
    }



    /**
     *
     * @return array
     *
     * Возвращает всех потомков, учитывая всю вложенность.
     * $id - id, для которого вернуть потомков.
     * $model - название модели в формате \App\Page.
     * $column - название колонки родителя, по-умолчанию parent_id, необязательный параметр.
     */
    public static function childrenId($id, $model, $column = 'parent_id')
    {
        if ((int)$id && $model) {
            $data = self::getDataKeyId($model);
            return self::getChildren($data, (int)$id);
        }
        return [];
    }

    private static function getChildren($data, $id, $column = 'parent_id')
    {
        if ($data && (int)$id) {
            $children = self::getFindChildrenId($data, [(int)$id], $column);
            if ($children) {
                return array_merge($children, self::getChildrenChildren($data, $children));
            }
        }
        return [];
    }

    private static function getChildrenChildren($data, $arrIDs, $column = 'parent_id')
    {
        if ($data && $arrIDs) {
            $children = self::getFindChildrenId($data, $arrIDs, $column) ?: [];
            $childrenChildren = [];
            if ($children) {
                $childrenChildren = self::getChildrenChildren($data, $children, $column);
            }
            return array_merge($children, $childrenChildren);
        }
        return [];
    }

    private static function getFindChildrenId($data, $arrIDs, $column = 'parent_id')
    {
        if ($data && $arrIDs) {
            $IDs = [];
            foreach ($data as $key => $value) {
                if (in_array($value[$column], $arrIDs)) {
                    $IDs[] = $value['id'];
                }
            }
            return $IDs;
        }
        return [];
    }
}
