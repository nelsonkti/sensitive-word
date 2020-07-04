<?php


namespace Nelsonkti\SensitiveWord;


class SensitiveWord
{
    /**
     * 替换码
     *
     * @var string
     */
    private $replaceCode = '*';

    /**
     * 敏感词库集合
     *
     * @var array
     */
    protected $trieTreeMap = array();

    /**
     * 干扰因子集合
     *
     * @var array
     */
    private $disturbList = array('*');

    /**
     * 文件路径
     *
     * @var string
     */
    private $filename = null;

    /**
     * 敏感词树
     *
     * @var array
     */
    private static $sensitiveWordTree = [];

    /**
     * 干扰因子集合
     *
     * @param array $disturbList
     */
    public function interference($disturbList = array())
    {
        $this->disturbList = $disturbList ?? $this->disturbList;
    }

    /**
     * 设置文件路径
     *
     * @param $filename
     */
    protected function setFileName($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * 获取文件内容
     *
     * @param $filename "文件路径"
     * @return \Generator
     * @throws \Exception
     */
    protected function getFileContent()
    {
        $handle = fopen($this->filename, 'r');

        if (!$handle) {
            throw new \Exception('open the file failed');
        }

        while (!feof($handle)) {
            yield str_replace(['\'', ' ', PHP_EOL, ','], '', fgets($handle));
        }

        fclose($handle);
    }

    /**
     * 生成敏感词库集合
     *
     * @param $filename "文件路径"
     * @throws \Exception
     */
    protected function generateWords()
    {
        // 获取文件内容
        $text = $this->getFileContent();

        foreach ($text as $key => $words) {
            $len = mb_strlen($words);
            $treeArr = &$this->trieTreeMap;
            for ($i = 0; $i < $len; $i++) {
                $word = mb_substr($words, $i, 1);
                //敏感词树结尾记录状态为false；
                if ($i + 1 == $len) {
                    $treeArr[$word]['end'] = false;
                }
                $treeArr = &$treeArr[$word] ?? false;
            }
        }
    }

    /**
     * 获取敏感词库集合
     *
     * @param $filename "文件路径"
     */
    private function getTrieTreeMap()
    {
        $trieTreeMap = &$this->trieTreeMap;

        if (!$trieTreeMap) {
            $this->generateWords($this->filename);
        }

        return $this;
    }

    /**
     * 匹配对应敏感词
     *
     * @param $txt "内容"
     * @param bool $hasReplace "是否替换原内容"
     * @param array $replaceCodeList "替换符合"
     * @return array
     */
    private function getWord($txt, $hasReplace = false, &$replaceCodeList = array())
    {
        $wordsList = $wordsList_arr = array();
        $txtLength = mb_strlen($txt);
        for ($i = 0; $i < $txtLength; $i++) {
            $wordLength = $this->checkWord($txt, $i, $txtLength);
            if ($wordLength > 0) {
                $words = mb_substr($txt, $i, $wordLength);

                if ($hasReplace) {
                    $wordsList_arr[] = array(
                        'lenth' => strlen($words),
                        'world' => $words,
                        'replace_code' => str_repeat($this->replaceCode, mb_strlen($words))
                    );
                } else {
                    $wordsList[] = $words;
                }

                $i += $wordLength - 1;
            }
        }

        $hasReplace && $wordsList = $this->sortWord($wordsList_arr, $replaceCodeList);

        return $wordsList;
    }

    /**
     * 对敏感词按长度进行倒叙排序
     *
     * @param $wordsList
     * @param $replaceCodeList
     * @param $txt
     */
    private function sortWord($wordsList_arr, &$replaceCodeList)
    {
        array_multisort(array_column($wordsList_arr, 'lenth'), SORT_DESC, $wordsList_arr);

        $replaceCodeList = array_column($wordsList_arr, 'replace_code');

        return array_column($wordsList_arr, 'world');
    }

    /**
     * 查找对应敏感词
     *
     * @param $txt "内容"
     * @param bool $hasReplace "是否替换原内容"
     * @param array $replaceCodeList "替换符合"
     * @return array
     */
    public function searchWord($txt, $filename)
    {
        return $this->setFileName($filename)->getTrieTreeMap()->getWord($txt);
    }

    /**
     * 过滤敏感词
     *
     * @param $txt "内容"
     * @param $filename "文件路径"
     * @return string|string[]
     */
    public function filterWord($txt, $filename)
    {
        $filename && $this->setFileName($filename)->interference();

        $replaceCodeList = array();

        $wordsList = $this->getTrieTreeMap()->getWord($txt, true, $replaceCodeList);


        return $wordsList ? str_replace($wordsList, $replaceCodeList, $txt) : $txt;
    }


    /**
     * 敏感词检测
     *
     * @param $txt "内容"
     * @param $begin "开始位置"
     * @param $length "长度"
     * @return int
     */
    private function checkWord($txt, $begin, $length)
    {
        $treeArr = &$this->trieTreeMap;
        $wordLength = 0; //敏感字符个数
        $wordLengthArray = [];
        $flag = false;
        for ($i = $begin; $i < $length; $i++) {
            $txtWord = mb_substr($txt, $i, 1);

            //如果搜索字不存在词库中直接停止循环。
            if (!isset($treeArr[$txtWord])) {
                break;
            }

            $wordLength++;
            if (isset($treeArr[$txtWord]['end'])) {
                $flag = true;
                $wordLengthArray[] = $wordLength;
            }
            $treeArr = &$treeArr[$txtWord];
        }

        $flag ?: $wordLength = 0;

        return $wordLength;
    }

}
