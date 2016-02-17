#快速开始#

检验一个身份证号码的格式是否正确，并且可以获取到生日，性别，办证区域

安装
===

`composer require cszchen/citizenid`

使用
===

	
	use cszchen\citizenid;
    
    $parser = new Parser();
    $parser->setId($id);
    
    //身份证号码格式是否正确
    $parser->isValidate();
    
    //获取生日，格式YYYYmmdd
    $parser->getBirthday();
    
    //获取性别
    $parser->getGenderLabel()
    
    //获取行政区域,返回数组包含省，市，县，完整区域
    $parser->getRegion();
	