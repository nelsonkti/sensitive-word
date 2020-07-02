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
    public $trieTreeMap = array();

    /**
     * 干扰因子集合
     *
     * @var array
     */
    private $disturbList = array();

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
        $this->disturbList = $disturbList;
    }

    /**
     * 添加敏感词
     *
     * @param array $txtWords
     */
    public function addWords($filename)
    {
        $text = $this->getGeneretor($filename);
        
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
     * 使用yield生成器
     *
     * @param $filename
     * @return \Generator
     * @throws \Exception
     */
    protected function getGeneretor($filename)
    {
        $handle = fopen($filename, 'r');
        if (!$handle) {
            throw new \Exception('read file failed');
        }
        while (!feof($handle)) {
            yield str_replace(['\'', ' ', PHP_EOL, ','], '', fgets($handle));
        }
        fclose($handle);
    }

    /**
     * 查找对应敏感词
     * @param $txt
     * @return array
     */
    public function search($txt, $hasReplace = false, &$replaceCodeList = array())
    {
        $wordsList = array();
        $txtLength = mb_strlen($txt);
        for ($i = 0; $i < $txtLength; $i++) {
            $wordLength = $this->checkWord($txt, $i, $txtLength);
            if ($wordLength > 0) {
                $words = mb_substr($txt, $i, $wordLength);
                $wordsList[] = $words;
                $hasReplace && $replaceCodeList[] = str_repeat($this->replaceCode, mb_strlen($words));
                $i += $wordLength - 1;
            }
        }

        return $wordsList;
    }

    /**
     * 过滤敏感词
     *
     * @param $txt
     * @return mixed
     */
    public function filter($txt)
    {
        $replaceCodeList = array();

        $wordsList = $this->search($txt, true, $replaceCodeList);
        if (empty($wordsList)) {
            return $txt;
        }
        return str_replace($wordsList, $replaceCodeList, $txt);
    }

    /**
     * 敏感词检测
     *
     * @param $txt
     * @param $beginIndex
     * @param $length
     * @return int
     */
    private function checkWord($txt, $beginIndex, $length)
    {
        $treeArr = &$this->trieTreeMap;
        $wordLength = 0; //敏感字符个数
        $wordLengthArray = [];
        $flag = false;
        for ($i = $beginIndex; $i < $length; $i++) {
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

    /**
     * 干扰因子检测
     *
     * @param $word
     * @return bool
     */
    private function checkDisturb($word)
    {
        return in_array($word, $this->disturbList);
    }
}
