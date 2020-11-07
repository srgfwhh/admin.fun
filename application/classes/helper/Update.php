<?php

namespace app\classes\helper;

use OSS\OssClient;
use think\Db;
use think\File;

require VENDOR_PATH . '/oss/autoload.php';

class Update
{

    protected $kv = [];

    //图片异步上传
    public function uploadpic(File $file, $type = '', $mulu = "temp")
    {
        if ($type == 'oss') {
            $this->ossStart();
            //目录
            $dir = "/img/" . $mulu . '/' . date('Ymd');
            $checkext = $file->validate(['size' => 1024 * 1024 * 5, 'ext' => 'jpg,jpeg,png,gif'])->check();
            if (!$checkext) {
                return errJson("请上传图片格式，且大小不超过5M！");
            }
            //正常的fileinfo => ['name'=>xxx.png,['type']=>image/png,['size']=>18380,[tmp_name]=>C:\Users\...,[error]=>0]
            $fileinfo = $file->getInfo();

            $fileNameArr = explode('.', $fileinfo['name']);
            $suffix = end($fileNameArr);
            $filename = substr(md5(uniqid(rand())), -12) . '.' . $suffix;//生成一个唯一的文件名

            $this->ossStart();
            $info = $this->putfiletooss_product($dir . '/' . $filename, $fileinfo['tmp_name'], $mulu);
            if (!$info) {
                return errJson("upload faile！");
            }
            //上传成功后 $info 为上传本地或oss的对象信息
            $data['src'] = $dir . '/' . $filename;
            $data['url'] = Config('img_domain') . $dir . '/' . $filename;
            return sucJson($data);
        }
        $dir = "static/img/" . $mulu;
        $info = $file->move(INDEX_PATH . $dir);
        if ($info) {
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            return sucJson(['src' => '/' . $dir . '/' . $info->getSaveName()]);
        } else {
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    function ossStart()
    {
        $oss = Db::table('jd_setting')->select();
        $oss = array_column($oss, null, 'title');
        $this->kv['AccessKeyID'] = $oss['AccessKeyID']['value'];
        $this->kv['AccessKeySecret'] = $oss['AccessKeySecret']['value'];
        $this->kv['OSS_ENDPOINT'] = $oss['OSS_ENDPOINT']['value'];
        $this->kv['OSS_BUCKET'] = $oss['OSS_BUCKET']['value'];
    }

    //删除文件
    public function delFile(string $filename, string $type)
    {
        if ($type == 'oss') {
            $ossClient = new OssClient($this->kv['AccessKeyID'], $this->kv['AccessKeySecret'], $this->kv['OSS_ENDPOINT']);
            $ossClient->deleteObject($this->kv['OSS_BUCKET'], $filename);
        } else {
            //原生删除
        }
    }

    protected function putfiletooss_product($filename, $filepath, $mulu)
    {
        $ossClient = new OssClient($this->kv['AccessKeyID'], $this->kv['AccessKeySecret'], $this->kv['OSS_ENDPOINT']);
        $rs = $ossClient->uploadFile($this->kv['OSS_BUCKET'], $filename, $filepath);
        if (isset($rs['oss-request-url'])) {
            $arr = explode($mulu, $rs['oss-request-url']);
            $rs['shortpath'] = $arr[1];
        }
        return $rs;
    }
}