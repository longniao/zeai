/*www_ZEAI_cn ZEAIv6_0高速JS缓存系统*/var heigh_ARR=[];for(var i=140;i<=210; i++){var istr=i.toString();heigh_ARR.push({i:istr,v:istr+" cm"});}var weigh_ARR=[];for(var i=40;i<=120; i++){var istr=i.toString();weigh_ARR.push({i:istr,v:istr+" kg"});}var age_ARR=[];for(var i=20;i<=80; i++){var istr=i.toString();age_ARR.push({i:istr,v:istr+" 岁"});}var pet_ARR=[{i:"1",v:"猫"},{i:"2",v:"狗"},{i:"3",v:"鸟"},{i:"4",v:"鱼"},{i:"5",v:"兔"},{i:"6",v:"鼠"},{i:"7",v:"猪"},{i:"8",v:"马"},{i:"9",v:"蛇"},{i:"10",v:"乌龟"},{i:"11",v:"爬行动物"},{i:"12",v:"另类宠物"},{i:"13",v:"其他宠物"},{i:"14",v:"正打算养"},{i:"15",v:"不喜欢养"},{i:"16",v:"过敏"}],tag1_ARR=[{i:"1",v:"孝顺男"},{i:"2",v:"酷男"},{i:"3",v:"责任心"},{i:"4",v:"经济适用男"},{i:"5",v:"憨直"},{i:"6",v:"感性男"},{i:"7",v:"事业男"},{i:"8",v:"睿智"},{i:"9",v:"猥琐男"},{i:"10",v:"幽默男"},{i:"11",v:"爱旅行"},{i:"12",v:"宅男"},{i:"13",v:"体贴"},{i:"14",v:"有魄力"},{i:"15",v:"仗义"},{i:"16",v:"经理人"},{i:"17",v:"帅哥"},{i:"18",v:"稳重"}],tag2_ARR=[{i:"1",v:"孝顺女"},{i:"2",v:"小资女"},{i:"3",v:"秀外慧中"},{i:"4",v:"理性女"},{i:"5",v:"感性女"},{i:"6",v:"善良"},{i:"7",v:"事业女"},{i:"8",v:"气质女"},{i:"9",v:"美女"},{i:"10",v:"居家女"},{i:"11",v:"爱运动"},{i:"12",v:"美食家"},{i:"13",v:"野蛮女友"},{i:"14",v:"宅女"},{i:"15",v:"直爽"},{i:"16",v:"爱小动物"},{i:"17",v:"小可爱"},{i:"18",v:"性感女"}],house_wz_ARR=[{i:"1",v:"市区"},{i:"2",v:"城区有房"},{i:"3",v:"老家有房"}],marrytype_ARR=[{i:"1",v:"嫁娶"},{i:"2",v:"两顾"},{i:"3",v:"上门"}],sex_ARR=[{i:"1",v:"男"},{i:"2",v:"女"}],love_ARR=[{i:"1",v:"未婚"},{i:"3",v:"离异"},{i:"4",v:"丧偶"}],edu_ARR=[{i:"1",v:"初中"},{i:"2",v:"高中"},{i:"3",v:"大专"},{i:"4",v:"本科"},{i:"5",v:"硕士"},{i:"6",v:"博士"}],house_ARR=[{i:"1",v:"已购房(有贷款)"},{i:"2",v:"已购房(无贷款)"},{i:"3",v:"有能力购房"},{i:"4",v:"无房"},{i:"5",v:"无房希望对方解决"},{i:"6",v:"无房希望双方解决"},{i:"7",v:"与父母同住"},{i:"8",v:"独自租房"},{i:"9",v:"与人合租"},{i:"10",v:"住亲朋家"},{i:"11",v:"住单位房"}],child_ARR=[{i:"1",v:"未育"},{i:"2",v:"子女归自己"},{i:"3",v:"子女归对方"}],car_ARR=[{i:"2",v:"无车"},{i:"1",v:"已购车-经济型"},{i:"3",v:"已购车-中档型"},{i:"4",v:"已购车-豪华型"},{i:"5",v:"单位用车"},{i:"6",v:"需要时购置"}],blood_ARR=[{i:"1",v:"A型"},{i:"2",v:"B型"},{i:"3",v:"AB型"},{i:"4",v:"O型"},{i:"5",v:"其他或未知"}],nation_ARR=[{i:"1",v:"汉族"},{i:"2",v:"藏族"},{i:"3",v:"朝鲜族"},{i:"4",v:"蒙古族"},{i:"5",v:"回族"},{i:"6",v:"满族"},{i:"7",v:"维吾尔族"},{i:"8",v:"壮族"},{i:"9",v:"彝族"},{i:"10",v:"苗族"},{i:"11",v:"侗族"},{i:"12",v:"瑶族"},{i:"13",v:"白族"},{i:"14",v:"布依族"},{i:"15",v:"傣族"},{i:"16",v:"京族"},{i:"17",v:"黎族"},{i:"18",v:"羌族"},{i:"19",v:"怒族"},{i:"20",v:"佤族"},{i:"21",v:"水族"},{i:"22",v:"畲族"},{i:"23",v:"土族"},{i:"24",v:"阿昌族"},{i:"25",v:"哈尼族"},{i:"26",v:"高山族"},{i:"27",v:"景颇族"},{i:"28",v:"珞巴族"},{i:"29",v:"锡伯族"},{i:"30",v:"德昂(崩龙)族"},{i:"31",v:"保安族"},{i:"32",v:"基诺族"},{i:"33",v:"门巴族"},{i:"34",v:"毛南族"},{i:"35",v:"赫哲族"},{i:"36",v:"裕固族"},{i:"37",v:"撒拉族"},{i:"38",v:"独龙族"},{i:"39",v:"普米族"},{i:"40",v:"仫佬族"},{i:"41",v:"仡佬族"},{i:"42",v:"东乡族"},{i:"43",v:"拉祜族"},{i:"44",v:"土家族"},{i:"45",v:"纳西族"},{i:"46",v:"傈僳族"},{i:"47",v:"布朗族"},{i:"48",v:"哈萨克族"},{i:"49",v:"达斡尔族"},{i:"50",v:"鄂伦春族"},{i:"51",v:"鄂温克族"},{i:"52",v:"俄罗斯族"},{i:"53",v:"塔塔尔族"},{i:"54",v:"塔吉克族"},{i:"55",v:"柯尔克孜族"},{i:"56",v:"乌兹别克族"}],smoking_ARR=[{i:"1",v:"不吸"},{i:"2",v:"偶尔吸"},{i:"3",v:"一天一包"},{i:"4",v:"有烟就吸"},{i:"5",v:"正在戒烟"},{i:"6",v:"已经戒了"}],pay_ARR=[{i:"1",v:"1千以下"},{i:"2",v:"1~2千"},{i:"3",v:"2~3千"},{i:"4",v:"3~4千"},{i:"5",v:"5~8千"},{i:"6",v:"8千~1万"},{i:"7",v:"1~2万"},{i:"8",v:"2~5万"},{i:"9",v:"5万以上"}],job_ARR=[{i:"1",v:"市场/销售"},{i:"2",v:"医生"},{i:"3",v:"律师"},{i:"4",v:"教师"},{i:"5",v:"幼师"},{i:"6",v:"设计师"},{i:"7",v:"程序员"},{i:"8",v:"策划推广"},{i:"9",v:"客服人员"},{i:"10",v:"空姐"},{i:"11",v:"护士"},{i:"12",v:"服务员"},{i:"13",v:"营业员"},{i:"14",v:"导游"},{i:"15",v:"记者/编辑"},{i:"16",v:"摄影师"},{i:"17",v:"文员/秘书"},{i:"18",v:"行政人事"},{i:"19",v:"高层管理"},{i:"20",v:"公务员"},{i:"21",v:"机关干部"},{i:"22",v:"军人"},{i:"23",v:"警察"},{i:"24",v:"消防员"},{i:"25",v:"工程师"},{i:"26",v:"职业经理人"},{i:"27",v:"咨询师"},{i:"28",v:"会计师"},{i:"29",v:"审计师"},{i:"30",v:"经纪人"},{i:"31",v:"预算员"},{i:"32",v:"检验员"},{i:"33",v:"理财师"},{i:"34",v:"出纳财务"},{i:"35",v:"技术人员"},{i:"36",v:"办公室职员"},{i:"37",v:"体育工作者"},{i:"38",v:"水电工"},{i:"39",v:"厨师"},{i:"40",v:"司机"},{i:"41",v:"操作工"},{i:"42",v:"机械师"},{i:"43",v:"美容化妆师"},{i:"44",v:"结构师"},{i:"45",v:"药剂师"},{i:"46",v:"建筑工"},{i:"47",v:"建筑包工头"},{i:"48",v:"生产采购"},{i:"49",v:"仓管专员"},{i:"50",v:"音乐家"},{i:"51",v:"画家"},{i:"52",v:"艺术家"},{i:"53",v:"模特"},{i:"54",v:"演艺人员"},{i:"55",v:"教练"},{i:"56",v:"讲师"},{i:"57",v:"教授"},{i:"58",v:"翻译"},{i:"59",v:"学生"},{i:"60",v:"私营业主"},{i:"61",v:"自由职业者"},{i:"62",v:"农民"},{i:"90",v:"待业中"},{i:"99",v:"其他"}],drink_ARR=[{i:"1",v:"滴酒不沾"},{i:"2",v:"有时喝一些"},{i:"3",v:"喜欢来两杯"},{i:"4",v:"好酒量，天天喝"},{i:"5",v:"正在戒酒"},{i:"6",v:"已经戒了"}],rest_ARR=[{i:"1",v:"早睡早起很规律"},{i:"2",v:"经常夜猫子"},{i:"3",v:"总是早起鸟"},{i:"4",v:"偶尔懒散一下"},{i:"5",v:"没有规律"}],sporthabit_ARR=[{i:"1",v:"每天锻炼"},{i:"2",v:"每周至少一次"},{i:"3",v:"每月几次"},{i:"4",v:"没时间锻炼"},{i:"5",v:"集中时间锻炼"},{i:"6",v:"不喜欢锻炼"}],parentsstatus_ARR=[{i:"1",v:"父母均健在"},{i:"2",v:"只有母亲健在"},{i:"3",v:"只有父亲健在"},{i:"4",v:"父母均已离世"}],broandsis_ARR=[{i:"1",v:"独生子女"},{i:"2",v:"2"},{i:"3",v:"3"},{i:"4",v:"4"},{i:"5",v:"5"},{i:"6",v:"6"},{i:"7",v:"7"},{i:"8",v:"8"},{i:"9",v:"9"}],parentslive_ARR=[{i:"1",v:"愿意"},{i:"2",v:"不愿意"},{i:"3",v:"视具体情况而定"},{i:"4",v:"尊重伴侣意见"}],companykind_ARR=[{i:"1",v:"政府机关"},{i:"2",v:"事业单位"},{i:"3",v:"外资企业"},{i:"4",v:"合资企业"},{i:"5",v:"国营企业"},{i:"6",v:"私营企业"},{i:"7",v:"自有公司"},{i:"8",v:"其他"}],jobfield_ARR=[{i:"1",v:"计算机/互联网（软件、硬件、服务）"},{i:"2",v:"通信/电子（半导体、仪器、自动化）"},{i:"3",v:"金融服务（会计、审计、银行、保险）"},{i:"4",v:"金融/投资/证券"},{i:"5",v:"贸易（进出口、批发、零售）"},{i:"6",v:"快速消费品（食品、饮料、化妆品）"},{i:"7",v:"服装/纺织/皮革"},{i:"8",v:"家具/家电/玩具/工艺品/小饰品"},{i:"9",v:"办公用品及设备"},{i:"10",v:"医疗/医药/保健"},{i:"11",v:"广告/公关/市场推广/会展"},{i:"12",v:"影视/媒体/出版/印刷/包装"},{i:"13",v:"房地产相关（建筑、物业）"},{i:"14",v:"家居/室内设计/装潢"},{i:"15",v:"服务（咨询、人力资源）"},{i:"16",v:"法律相关"},{i:"17",v:"教育/培训"},{i:"18",v:"学术/科研"},{i:"19",v:"酒店/餐饮业"},{i:"20",v:"旅游"},{i:"21",v:"娱乐/休闲/体育"},{i:"22",v:"美容/保健/形体"},{i:"23",v:"交通（运输、物流、航天、航空）"},{i:"24",v:"汽车（含销售、零配件、相关服务）"},{i:"25",v:"农业（农、林、牧、渔业）"},{i:"26",v:"政府机构/机关"},{i:"27",v:"艺术/音乐/舞蹈"},{i:"28",v:"制造/化工/能源"},{i:"90",v:"非盈利机构"},{i:"99",v:"其他行业"}],marrytime_ARR=[{i:"1",v:"随时"},{i:"2",v:"半年内"},{i:"3",v:"一年内"},{i:"4",v:"两年内"},{i:"5",v:"三年内"}],crm_ukind_ARR=[{i:"1",v:"新分未联系"},{i:"2",v:"联系未接通"},{i:"3",v:"明确有需求"},{i:"4",v:"意向已明确"},{i:"5",v:"一周内重点"},{i:"6",v:"一天内重点"},{i:"7",v:"报价跟进中"},{i:"8",v:"已签约"},{i:"9",v:"寻找中"},{i:"10",v:"交往中"},{i:"11",v:"未找到"},{i:"12",v:"已找到"},{i:"13",v:"暂停中"},{i:"14",v:"情感咨询"},{i:"15",v:"冷宫"}],crm_ugrade_ARR=[{i:"1",v:"一星客户"},{i:"2",v:"二星客户"},{i:"3",v:"三星客户"},{i:"4",v:"四星客户"},{i:"5",v:"五星客户"}];