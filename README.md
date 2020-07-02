# sensitive-word

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
    'SensitiveWord' => Nelsonkti\SensitiveWord\SensitiveWordFacade::class,
],
```

## Usage

基本使用:


```
$path = './word.txt'

// 添加敏感词
SensitiveWordFacade::addwords($path);

// content：检查内容
SensitiveWord::search($content, true)
```

## License

sensitive-word is licensed under [The MIT License (MIT)](https://github.com/nelsonkti/sensitive-word/blob/master/README.md).