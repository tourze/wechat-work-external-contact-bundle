<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\Attachment\File;
use WechatWorkExternalContactBundle\Request\Attachment\Image;
use WechatWorkExternalContactBundle\Request\Attachment\Link;
use WechatWorkExternalContactBundle\Request\Attachment\MiniProgram;
use WechatWorkExternalContactBundle\Request\Attachment\Video;

/**
 * BaseAttachment 抽象类测试
 *
 * @internal
 */
#[CoversClass(BaseAttachment::class)]
#[RunTestsInSeparateProcesses] final class BaseAttachmentTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void        // 此测试不需要特殊的设置
    {
    }

    public function testIsAbstract(): void
    {
        // 测试是抽象类
        $reflection = new \ReflectionClass(BaseAttachment::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function testImplementsPlainArrayInterface(): void
    {
        // 测试实现PlainArrayInterface接口
        $reflection = new \ReflectionClass(BaseAttachment::class);
        $this->assertTrue($reflection->implementsInterface(PlainArrayInterface::class));
    }

    public function testCannotBeInstantiatedDirectly(): void
    {
        // 测试无法直接实例化抽象类
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot instantiate abstract class');

        // 直接通过反射尝试实例化
        $reflection = new \ReflectionClass(BaseAttachment::class);
        $reflection->newInstance();
    }

    public function testImageExtendsBaseAttachment(): void
    {
        // 测试Image类继承BaseAttachment
        $image = self::getService(Image::class);
        $this->assertInstanceOf(BaseAttachment::class, $image);
        $image->setMediaId('test_media_id');
        $this->assertSame('test_media_id', $image->getMediaId());
    }

    public function testFileExtendsBaseAttachment(): void
    {
        // 测试File类继承BaseAttachment
        $file = self::getService(File::class);
        $this->assertInstanceOf(BaseAttachment::class, $file);
        $file->setMediaId('test_file_media');
        $this->assertSame('test_file_media', $file->getMediaId());
    }

    public function testVideoExtendsBaseAttachment(): void
    {
        // 测试Video类继承BaseAttachment
        $video = self::getService(Video::class);
        $this->assertInstanceOf(BaseAttachment::class, $video);
        $video->setMediaId('test_video_media');
        $this->assertSame('test_video_media', $video->getMediaId());
    }

    public function testLinkExtendsBaseAttachment(): void
    {
        // 测试Link类继承BaseAttachment
        $link = self::getService(Link::class);
        $this->assertInstanceOf(BaseAttachment::class, $link);
        $link->setTitle('测试链接标题');
        $this->assertSame('测试链接标题', $link->getTitle());
    }

    public function testMiniProgramExtendsBaseAttachment(): void
    {
        // 测试MiniProgram类继承BaseAttachment
        $miniProgram = self::getService(MiniProgram::class);
        $this->assertInstanceOf(BaseAttachment::class, $miniProgram);
        $miniProgram->setTitle('测试小程序');
        $this->assertSame('测试小程序', $miniProgram->getTitle());
    }

    public function testAllConcreteClassesImplementRetrievePlainArray(): void
    {
        // 测试所有具体类都实现了retrievePlainArray方法
        $attachmentClasses = [
            Image::class,
            File::class,
            Video::class,
            Link::class,
            MiniProgram::class,
        ];

        foreach ($attachmentClasses as $className) {
            $reflection = new \ReflectionClass($className);
            $this->assertTrue($reflection->hasMethod('retrievePlainArray'));
            $this->assertTrue($reflection->getMethod('retrievePlainArray')->isPublic());
        }
    }

    public function testBaseAttachmentClassStructure(): void
    {
        // 测试BaseAttachment类结构
        $reflection = new \ReflectionClass(BaseAttachment::class);

        $this->assertTrue($reflection->isAbstract());
        $this->assertFalse($reflection->isFinal());
        $this->assertTrue($reflection->implementsInterface(PlainArrayInterface::class));

        // BaseAttachment是一个简单的抽象类，只实现接口但不定义额外方法
        $this->assertEmpty($reflection->getProperties());
    }

    public function testBusinessScenarioPolymorphism(): void
    {
        // 测试业务场景：多态性
        $attachments = [];

        // 创建不同类型的附件
        $image = self::getService(Image::class);
        $image->setMediaId('test_image_media');

        $file = self::getService(File::class);
        $file->setMediaId('test_file_media');

        $video = self::getService(Video::class);
        $video->setMediaId('test_video_media');

        $link = self::getService(Link::class);
        $link->setTitle('测试链接');
        $link->setUrl('https://example.com');

        $miniProgram = self::getService(MiniProgram::class);
        $miniProgram->setTitle('测试小程序');
        $miniProgram->setPicMediaId('test_pic_media');
        $miniProgram->setAppId('wx_test_app');
        $miniProgram->setPage('pages/test/index');

        $attachments = [$image, $file, $video, $link, $miniProgram];

        // 验证都是BaseAttachment的实例
        foreach ($attachments as $attachment) {
            $this->assertInstanceOf(BaseAttachment::class, $attachment);
            $this->assertInstanceOf(PlainArrayInterface::class, $attachment);

            // 验证都能调用retrievePlainArray方法
            $array = $attachment->retrievePlainArray();
            $this->assertArrayHasKey('msgtype', $array);
        }
    }

    public function testBusinessScenarioAttachmentTypes(): void
    {
        // 测试业务场景：不同附件类型验证
        $testCases = [
            [Image::class, 'image'],
            [File::class, 'file'],
            [Video::class, 'video'],
            [Link::class, 'link'],
            [MiniProgram::class, 'miniprogram'],
        ];

        foreach ($testCases as [$className, $expectedMsgType]) {
            $reflection = new \ReflectionClass($className);
            $this->assertTrue($reflection->isSubclassOf(BaseAttachment::class));

            // 每个具体类都应该有对应的msgtype
            $instance = $this->createMockInstanceForTesting($className);
            if (null !== $instance) {
                $array = $instance->retrievePlainArray();
                $this->assertSame($expectedMsgType, $array['msgtype']);
            }
        }
    }

    public function testInterfaceContract(): void
    {
        // 测试接口契约
        $interface = new \ReflectionClass(PlainArrayInterface::class);
        $methods = $interface->getMethods();

        $this->assertCount(1, $methods);
        $this->assertSame('retrievePlainArray', $methods[0]->getName());

        // 验证BaseAttachment确实实现了这个接口
        $baseAttachment = new \ReflectionClass(BaseAttachment::class);
        $this->assertTrue($baseAttachment->implementsInterface(PlainArrayInterface::class));
    }

    public function testAbstractClassCannotBeInstantiated(): void
    {
        // 测试抽象类无法实例化的另一种方式
        $reflection = new \ReflectionClass(BaseAttachment::class);
        $this->assertTrue($reflection->isAbstract());

        // 验证尝试通过反射实例化会失败
        $this->expectException(\Error::class);
        $reflection->newInstance();
    }

    /**
     * 为测试创建模拟实例
     */
    private function createMockInstanceForTesting(string $className): ?BaseAttachment
    {
        switch ($className) {
            case Image::class:
                $instance = self::getService(Image::class);
                $instance->setMediaId('test_media');

                return $instance;

            case File::class:
                $instance = self::getService(File::class);
                $instance->setMediaId('test_media');

                return $instance;

            case Video::class:
                $instance = self::getService(Video::class);
                $instance->setMediaId('test_media');

                return $instance;

            case Link::class:
                $instance = self::getService(Link::class);
                $instance->setTitle('Test');
                $instance->setUrl('https://test.com');

                return $instance;

            case MiniProgram::class:
                $instance = self::getService(MiniProgram::class);
                $instance->setTitle('Test');
                $instance->setPicMediaId('test_pic');
                $instance->setAppId('wx_test');
                $instance->setPage('pages/test');

                return $instance;

            default:
                return null;
        }
    }
}
