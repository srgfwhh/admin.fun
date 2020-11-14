### 参与开发
*   wanghuanhuan   
### 描述
*   tp5.0开发界面是简单的layui的iframe版，一个简单的cms基础后台
*   v1.会开放公众号模块，基于swoole和easyWechat组件,动态配置和发送公众号消息
*   v2.会开放聊天模块，基于websocket的浏览器聊天系统
*   v3.会配置化内置easySwoole框架的任务处理器，支持持久化任务操作，任务系统异常调度补发
*   已实现节点管理，动态权限判断，基于角色和节点的动态树状菜单

### 用法
*  使用前请执行 composer install 安装依赖包
*  需要权限验证的，请继承application下的common控制器，因用的是tp5.0使用没有控制器中间件使用，版本较低所以粗暴了一点

### 目录
~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─common             公共模块目录（可以更改）
│  ├─module_name        模块目录
│  │  ├─config.php      模块配置文件
│  │  ├─common.php      模块函数文件
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  │  └─ ...            更多类库目录
│  │
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~
