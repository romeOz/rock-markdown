<?php

namespace rockunit;


use League\Flysystem\Adapter\Local;
use rock\base\Alias;
use rock\file\FileManager;
use rock\helpers\FileHelper;
use rock\image\ImageProvider;
use rock\markdown\Markdown;

/**
 * @group base
 */
class MarkdownTest extends \PHPUnit_Framework_TestCase
{
    protected function getMarkdown(array $config = [])
    {
        return new Markdown($config);
    }
    public function testVideoInline()
    {
        $result = $this->getMarkdown()->parseParagraph('![:youtube 480x360](6JvDSwFtEC0 "title"){.class1 #id1 .class2}');
        $this->assertSame(
            '<iframe src="//youtube.com/embed/6JvDSwFtEC0/" title="title" width="480" height="360" allowfullscreen="allowfullscreen" frameborder="0" class="class1 class2" id="id1"></iframe>',
            $result
        );
    }

    public function testVideoSuccess()
    {
        $result = $this->getMarkdown()->parse('![:youtube 480x360][video]
Test

[video]: 6JvDSwFtEC0 {.class1 #id1 .class2}');
        $this->assertSame(
            '<p><iframe src="//youtube.com/embed/6JvDSwFtEC0/" width="480" height="360" allowfullscreen="allowfullscreen" frameborder="0" class="class1 class2" id="id1"></iframe>
Test</p>',
            $result
        );
    }

    public function testVideoFail()
    {
        $config = [
            'denyTags' => ['video']
        ];
        $result = $this->getMarkdown($config)->parse('![:youtube 480x360](6JvDSwFtEC0){.class1 #id1 .class2}');
        $this->assertSame(
            $result,
            '<p><img src="6JvDSwFtEC0" alt="" class="class1 class2" id="id1" /></p>'
        );
    }

    public function testVideoDummy()
    {
        $config = [
            'dummy' => Markdown::DUMMY,
            'specialAttributesDummy' => '.dummy-video'
            //'denyTags' => ['code']
        ];
        $result = $this->getMarkdown($config)->parse('![:youtube 480x360][video]
Test

[video]: 6JvDSwFtEC0 {.class1 #id1 .class2}');
        $this->assertSame(
            '<p><a href="https://www.youtube.com/watch?v=6JvDSwFtEC0" style="width: 480px; height: 360px" target="_blank" rel="nofollow"  class="dummy-video class1 class2" id="id1" ></a>
Test</p>',
            $result
        );
    }

    public function testTable()
    {
        $result = $this->getMarkdown()->parse('
{.class1 #id1 .class1}
| header_1 | header_2 | header_3 |
|:--| :--- | :---: |
| **Foo** | bar | 123 |

');
        $this->assertSame(
        '<table class="class1 class1" id="id1">
<thead>
<tr><th align="left"> header_1 </th><th align="left">header_2 </th><th align="center">header_3 </th></tr>
</thead>
<tbody>
<tr><td align="left"> <strong>Foo</strong> </td><td align="left">bar </td><td align="center">123 </td></tr>
</tbody>
</table>',
        $result
        );
    }

    public function testLinkInline()
    {
        $result = $this->getMarkdown()->parseParagraph('[text](http://test/ "title text"){.class1 #id1 .class2}');
        $this->assertSame(
            '<a href="http://test/" title="title text" class="class1 class2" id="id1"  rel="nofollow" target="_blank">text</a>',
            $result
        );

        // empty note
        $result = $this->getMarkdown()->parseParagraph('[](http://test/ "title text"){.class1 #id1 .class2}');
        $this->assertSame(
            '<a href="http://test/" title="title text" class="class1 class2" id="id1"  rel="nofollow" target="_blank">link</a>',
            $result
        );
    }

    public function testLink()
    {
        $result = $this->getMarkdown()->parse('[text][link]
Test

[link]: http://test/ {.class1 #id1 .class2}');
        $this->assertSame(
            '<p><a href="http://test/" class="class1 class2" id="id1"  rel="nofollow" target="_blank">text</a>
Test</p>',
            $result
        );
    }

    public function testThumb()
    {
        $mark = $this->getMarkdown();
        $this->assertSame(
            '<p><img src="/data/play.png" alt="foo" class="class2 class" id="id2" /></p>',
            $mark->parse('![foo](/data/play.png){.class2 #id2 .class}')
        );

        if (!class_exists('\rock\file\FileManager') || !class_exists('\League\Flysystem\Filesystem')) {
            $this->markTestSkipped('FileManager not installed.');
        }

        $mark = $this->getMarkdown(['imageProvider' => $this->getImageProvider()]);
        $this->assertSame(
            '<p><img src="/data/cache/50x50/play.png" alt="" class="class2 class" id="id2" /></p>',
            $mark->parse('![:thumb 50x50](/data/play.png){.class2 #id2 .class}')
        );

        $this->assertSame(
            '<p><img src="/data/play.png" alt="" class="class2 class" id="id2" /></p>',
            $mark->parse('![:thumb](/data/play.png){.class2 #id2 .class}')
        );

        // fail

        $mark = $this->getMarkdown(['imageProvider' => $this->getImageProvider()]);
        $this->assertSame(
            '<p><img src="/data/foo.png" alt="" class="class2 class" id="id2" /></p>',
            $mark->parse('![:thumb 50x50](/data/foo.png){.class2 #id2 .class}')
        );

        $mark->denyTags = ['thumb'];
        $this->assertSame(
            '<p><img src="/data/foo.png" alt="" class="class2 class" id="id2" /></p>',
            $mark->parse('![:thumb 50x50](/data/foo.png){.class2 #id2 .class}')
        );
    }

    public function testDenyTags()
    {
        $config = ['denyTags' => ['class']];
        $result = $this->getMarkdown($config)->parse('h1 {.class1 #id1 .class2}
==

text');
        $this->assertSame(
            '<h1>h1</h1>
<p>text</p>',
            $result
        );
    }

    public function testCodeFail()
    {
        $markdown = $this->getMarkdown(['denyTags' => ['code']]);
        $this->assertSame($markdown->parse('     foo'), '');
        $this->assertSame(
            '<p>foo</p>
<p>bar</p>',
            $markdown->parse('
foo

```php
            gjh

```

bar')
        );
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        static::clearRuntime();
    }


    protected function getImageProvider()
    {
        return new ImageProvider(
            [
                'srcImage' => '/data',
                'srcCache' => '/data/cache',
                'adapter' =>   [
                    'class' => FileManager::className(),
                    'adapter' => new Local(Alias::getAlias('@rockunit/data'))
                ],
                'adapterCache' => [
                    'class' => FileManager::className(),
                    'adapter' => new Local(Alias::getAlias('@rockunit/runtime')),
                ]
            ]
        );
    }

    protected static function clearRuntime()
    {
        FileHelper::deleteDirectory(Alias::getAlias('@rockunit/runtime'));
    }
}