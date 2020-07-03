# sensitive-word
过滤敏感词，采用 DFA 算法
> 增加包含词的过滤（如：敏感、敏感词）

## Installation

```shell
$ composer require nelsonkti/sensitive-word
```

## Laravel
> 引导服务 config/app.php
```
'providers' => [
    Nelsonkti\SensitiveWord\SensitiveWordServiceProvider::class,
],

'aliases' => [
    'SensitiveWord' => Nelsonkti\SensitiveWord\SensitiveWord::class,
],
```

## Usage

基本使用:


```
$path = './word.txt'


$content = '敏感，敏感词'; #检查内容
$filename = '/txt/sensitive-words.txt'； #敏感词文件路径

SensitiveWord::search($content, $filename)

```

## License

sensitive-word is licensed under [The MIT License (MIT)](https://github.com/nelsonkti/sensitive-word/blob/master/LICENSE).