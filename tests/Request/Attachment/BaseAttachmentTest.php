<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Tourze\Arrayable\PlainArrayInterface;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\Attachment\File;
use WechatWorkExternalContactBundle\Request\Attachment\Image;
use WechatWorkExternalContactBundle\Request\Attachment\Link;
use WechatWorkExternalContactBundle\Request\Attachment\MiniProgram;
use WechatWorkExternalContactBundle\Request\Attachment\Video;

/**
 * BaseAttachment 抽象类测试
 */
class BaseAttachmentTest extends TestCase
{
    public function test_isAbstract(): void
    {
        // 测试是抽象类
        $reflection = new ReflectionClass(BaseAttachment::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function test_implementsPlainArrayInterface(): void
    {
        // 测试实现PlainArrayInterface接口
        $reflection = new ReflectionClass(BaseAttachment::class);
        $this->assertTrue($reflection->implementsInterface(PlainArrayInterface::class));
    }

    public function test_cannotBeInstantiatedDirectly(): void
    {
        // 测试无法直接实例化抽象类
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot instantiate abstract class');
        
        // 使用eval来避免静态分析错误
        eval('new ' . BaseAttachment::class . '();');
    }

    public function test_imageExtendsBaseAttachment(): void
    {
        // 测试Image类继承BaseAttachment
        $image = new Image();
        $this->assertInstanceOf(BaseAttachment::class, $image);
        $this->assertInstanceOf(PlainArrayInterface::class, $image);
    }

    public function test_fileExtendsBaseAttachment(): void
    {
        // 测试File类继承BaseAttachment
        $file = new File();
        $this->assertInstanceOf(BaseAttachment::class, $file);
        $this->assertInstanceOf(PlainArrayInterface::class, $file);
    }

    public function test_videoExtendsBaseAttachment(): void
    {
        // 测试Video类继承BaseAttachment
        $video = new Video();
        $this->assertInstanceOf(BaseAttachment::class, $video);
        $this->assertInstanceOf(PlainArrayInterface::class, $video);
    }

    public function test_linkExtendsBaseAttachment(): void
    {
        // 测试Link类继承BaseAttachment
        $link = new Link();
        $this->assertInstanceOf(BaseAttachment::class, $link);
        $this->assertInstanceOf(PlainArrayInterface::class, $link);
    }

    public function test_miniProgramExtendsBaseAttachment(): void
    {
        // 测试MiniProgram类继承BaseAttachment
        $miniProgram = new MiniProgram();
        $this->assertInstanceOf(BaseAttachment::class, $miniProgram);
        $this->assertInstanceOf(PlainArrayInterface::class, $miniProgram);
    }

    public function test_allConcreteClassesImplementRetrievePlainArray(): void
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
            $reflection = new ReflectionClass($className);
            $this->assertTrue($reflection->hasMethod('retrievePlainArray'));
            $this->assertTrue($reflection->getMethod('retrievePlainArray')->isPublic());
        }
    }

    public function test_baseAttachmentClassStructure(): void
    {
        // 测试BaseAttachment类结构
        $reflection = new ReflectionClass(BaseAttachment::class);
        
        $this->assertTrue($reflection->isAbstract());
        $this->assertFalse($reflection->isFinal());
        $this->assertTrue($reflection->implementsInterface(PlainArrayInterface::class));
        
        // BaseAttachment是一个简单的抽象类，只实现接口但不定义额外方法
        $this->assertEmpty($reflection->getProperties());
    }

    public function test_businessScenario_polymorphism(): void
    {
        // 测试业务场景：多态性
        $attachments = [];
        
        // 创建不同类型的附件
        $image = new Image();
        $image->setMediaId('test_image_media');
        
        $file = new File();
        $file->setMediaId('test_file_media');
        
        $video = new Video();
        $video->setMediaId('test_video_media');
        
        $link = new Link();
        $link->setTitle('测试链接');
        $link->setUrl('https://example.com');
        
        $miniProgram = new MiniProgram();
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
            $this->assertIsArray($array);
            $this->assertArrayHasKey('msgtype', $array);
        }
    }

    public function test_businessScenario_attachmentTypes(): void
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
            $reflection = new ReflectionClass($className);
            $this->assertTrue($reflection->isSubclassOf(BaseAttachment::class));
            
            // 每个具体类都应该有对应的msgtype
            $instance = $this->createMockInstanceForTesting($className);
            if ($instance) {
                $array = $instance->retrievePlainArray();
                $this->assertSame($expectedMsgType, $array['msgtype']);
            }
        }
    }

    public function test_interfaceContract(): void
    {
        // 测试接口契约
        $interface = new ReflectionClass(PlainArrayInterface::class);
        $methods = $interface->getMethods();
        
        $this->assertCount(1, $methods);
        $this->assertSame('retrievePlainArray', $methods[0]->getName());
        
        // 验证BaseAttachment确实实现了这个接口
        $baseAttachment = new ReflectionClass(BaseAttachment::class);
        $this->assertTrue($baseAttachment->implementsInterface(PlainArrayInterface::class));
    }

    public function test_abstractClassCannotBeInstantiated(): void
    {
        // 测试抽象类无法实例化的另一种方式
        $reflection = new ReflectionClass(BaseAttachment::class);
        $this->assertTrue($reflection->isAbstract());
        
        // 验证尝试通过反射实例化会失败
        $this->expectException(\Error::class);
        $reflection->newInstance();
    }

    /**
     * 为测试创建模拟实例
     */
    private function createMockInstanceForTesting(string $className)
    {
        switch ($className) {
            case Image::class:
                $instance = new Image();
                $instance->setMediaId('test_media');
                return $instance;
                
            case File::class:
                $instance = new File();
                $instance->setMediaId('test_media');
                return $instance;
                
            case Video::class:
                $instance = new Video();
                $instance->setMediaId('test_media');
                return $instance;
                
            case Link::class:
                $instance = new Link();
                $instance->setTitle('Test');
                $instance->setUrl('https://test.com');
                return $instance;
                
            case MiniProgram::class:
                $instance = new MiniProgram();
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