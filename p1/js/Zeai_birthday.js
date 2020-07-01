var selstr1 = '不限';
var selstr2 = '请选择';
function ZEAI_area_tab(ul,areauldl){
    var dtarr = areauldl.getElementsByTagName("dt");
    ZEAI_area_delfclass(ul,dtarr);
    for(var i=0; i<dtarr.length;i++){(function(i){
        dtarr[i].onclick = function(){
            ZEAI_area_delfclass(ul,dtarr);
            this.className = 'ed';
            var j=i+1;
            o(ul+'a'+j+'box').show();
        }
    })(i);}
}
function area_bx(leval,parent){
    if(leval==1){
        var newA1 = [{'i':'ZE0000','v':selstr1,f:0}];
        return newA1.concat(areaARR1);
    }
    if(leval==2){
        var newA2 = [];
        function newA2fn(){
            for(var k=0;k<areaARR1.length;k++){newA2.push({'i':'AI'+k+'00','v':selstr1,f:parent[k].i});}
            return newA2;
        }
        return newA2fn().concat(areaARR2);
    }
    if(leval==3){
        var newA3 = [];
        function newA3fn(){
            for(var k=0;k<areaARR2.length;k++) {newA3.push({'i':'CN00'+k,'v':selstr1,f:parent[k].i});}
            return newA3;
        }
        return newA3fn().concat(areaARR3);
    }
}
function ZEAI_area(ul,bx,selstr){
    if(bx){
        var ARR1=area_bx(1),ARR2=area_bx(2,ARR1),ARR3=area_bx(3,ARR2);
    }else{
        var ARR1=areaARR1,ARR2=areaARR2,ARR3=areaARR3;
    }
    var areaul   = o(ul),
        areaulli = areaul.children[1],
        areauldl = areaulli.getElementsByTagName("dl")[0],
        a1t='',a2t='',a3t='',area1id,area2id,area3id,
        area1id = o(ul+'area1id').value,
        area2id = o(ul+'area2id').value,
        area3id = o(ul+'area3id').value,
        areatitle = areaul.getElementsByTagName("span")[0];
    areaul.onmouseover = function (){areaulli.show();}
    areaul.onmouseout = function (){areaulli.hide();}
    var em1 = zeai.addtag('em');em1.id=ul+'a1box';
    var dt1 = zeai.addtag('dt');dt1.innerHTML='选择省份';dt1.className = 'ed';dt1.id = ul+'dt1id';
    for(var k1=0;k1<ARR1.length;k1++) {(function(k1){
        var A1  = zeai.addtag('a');
        A1.innerHTML = ARR1[k1].v;
        A1.onclick = function (){
            var em2 = o(ul+'a2box'),em3 = o(ul+'a3box'),dt2 = o(ul+'dt2id'),dt3 = o(ul+'dt3id');
            if (!zeai.empty(em2))em2.parentNode.removeChild(em2);
            if (!zeai.empty(em3))em3.parentNode.removeChild(em3);
            if (!zeai.empty(dt2))dt2.parentNode.removeChild(dt2);
            if (!zeai.empty(dt3))dt3.parentNode.removeChild(dt3);
            ZEAI_area_tab(ul,areauldl);dt1.innerHTML = this.innerHTML;
            var em2 = ZEAI_creat_area2(ARR2,ARR3,ul,areauldl,areaulli,areatitle,ARR1[k1].i);areaulli.appendChild(em2);em1.hide();
            ZEAI_delclass(em1.getElementsByTagName("a"));this.className = 'ed';//保持已选中
            o(ul+'area1id').value = ARR1[k1].i;
            o(ul+'dt2id').className = 'ed';
        }
        if (area1id == ARR1[k1].i){A1.className = 'ed';dt1.innerHTML = A1.innerHTML;}
        em1.appendChild(A1);
    })(k1);}
    areaulli.appendChild(em1);areauldl.appendChild(dt1);
    //
    if (zeai.ifint(area1id)){
        em2 = ZEAI_creat_area2(ARR2,ARR3,ul,areauldl,areaulli,areatitle,area1id);areaulli.appendChild(em2);ZEAI_area_tab(ul,areauldl);
        dt1.className = 'ed';o(ul+'a1box').show();o(ul+'a2box').hide();o(ul+'dt2id').className = '';
        a1t = dt1.innerHTML;
        if (zeai.ifint(area2id)){
            em3 = ZEAI_creat_area3(ARR3,ul,areauldl,areaulli,areatitle,area2id);areaulli.appendChild(em3);ZEAI_area_tab(ul,areauldl);
            dt1.className = 'ed';o(ul+'a1box').show();
            a2t = o(ul+'dt2id').innerHTML;
            if (zeai.ifint(area3id))a3t = o(ul+'dt3id').innerHTML;
            areatitle.style.color='#333';
        }
        areatitle.innerHTML = a1t +' '+ a2t +' ' + a3t;
        o('areatitle').value = areatitle.innerHTML;

    }else{
        selstr =(zeai.empty(selstr))?selstr2:selstr;
        areatitle.innerHTML = selstr;
    }
}
function ZEAI_creat_area2(ARR2,ARR3,ul,areauldl,areaulli,areatitle,area1id,area1title){
    var em2 = zeai.addtag('em');em2.id=ul+'a2box';
    var dt2 = zeai.addtag('dt');dt2.innerHTML='选择城市';dt2.id = ul+'dt2id';
    for(var k2=0;k2<ARR2.length;k2++) {(function(k2){
        if (ARR2[k2].f == area1id){
            var A2  = zeai.addtag('a');
            A2.innerHTML = ARR2[k2].v;
            A2.onclick = function (){
                var em3 = o(ul+'a3box');
                var dt3 = o(ul+'dt3id');
                if (!zeai.empty(em3))em3.parentNode.removeChild(em3);
                if (!zeai.empty(dt3))dt3.parentNode.removeChild(dt3);
                dt2.innerHTML = this.innerHTML;ZEAI_area_tab(ul,areauldl);
                var em3 = ZEAI_creat_area3(ARR3,ul,areauldl,areaulli,areatitle,ARR2[k2].i);areaulli.appendChild(em3);
                em2.hide();em3.show();
                ZEAI_delclass(em2.getElementsByTagName("a"));this.className = 'ed';//保持已选中
                o(ul+'area2id').value = ARR2[k2].i;
                o(ul+'dt3id').className = 'ed';
            }
            if (o(ul+'area2id').value == ARR2[k2].i){
                A2.className = 'ed';dt2.innerHTML = A2.innerHTML;
            }
            em2.appendChild(A2);
        }
    })(k2);}
    areauldl.appendChild(dt2);areaulli.className = '';
    return em2;
}
function ZEAI_creat_area3(ARR3,ul,areauldl,areaulli,areatitle,area2id){
    var em3 = zeai.addtag('em');em3.id=ul+'a3box';
    var dt3 = zeai.addtag('dt');dt3.innerHTML='选择区县';dt3.id = ul+'dt3id';
    for(var k3=0;k3<ARR3.length;k3++) {(function(k3){
        if (ARR3[k3].f == area2id){
            var A3  = zeai.addtag('a');
            A3.innerHTML = ARR3[k3].v;
            A3.onclick = function (){
                dt3.innerHTML = this.innerHTML;ZEAI_area_tab(ul,areauldl);o(ul+'dt1id').className = 'ed';
                o(ul+'a1box').show();areaulli.hide();
                ZEAI_delclass(em3.getElementsByTagName("a"));this.className = 'ed';//保持已选中
                areatitle.innerHTML = o(ul+'dt1id').innerHTML +' '+ o(ul+'dt2id').innerHTML +' ' + o(ul+'dt3id').innerHTML;
                o(ul+'area3id').value = ARR3[k3].i;
                o('areatitle').value = areatitle.innerHTML;
                areatitle.style.color='#333';
            }
            if (o(ul+'area3id').value == ARR3[k3].i){A3.className = 'ed';dt3.innerHTML = A3.innerHTML;}
            em3.appendChild(A3);
        }
    })(k3);}
    areauldl.appendChild(dt3);
    return em3;
}
function ZEAI_area_delfclass(ul,dtarr){for(var i=0; i<dtarr.length;i++){
    if (!zeai.empty(dtarr[i]))dtarr[i].className = '';
    var j=i+1;
    if (!zeai.empty(o(ul+'a'+j+'box')))o(ul+'a'+j+'box').hide();
}}
function prepend(arr, item) {
    var newarr=arr.slice(0);
    newarr.unshift(item);
    return newarr;
}
function ZEAI_delclass(arr){for(var i=0; i<arr.length;i++){arr[i].className = '';}}
function ZEAI_select(objstr,id,bx,selstr){
    var so_id = o(objstr+'_'+id),
        span   = so_id.getElementsByTagName("span")[0],
        formid = o(id),
        ARR    = eval(id+'_ARR'),
        so_li  = so_id.children[1];
    selstr =(zeai.empty(selstr))?selstr2:selstr;
    span.innerHTML = selstr;
    if (id == 'sex'){ARR[0].v += '友';ARR[1].v += '友';}
    if(bx){
        ARR = prepend(ARR,{i:0,v:"不限"});
    }
    for(var k=0;k<ARR.length;k++) {(function(k){
        if (id == 'age1' || id == 'age2'){
            ARR[k].v = ARR[k].v.replace('岁','');
        }
        if (id == 'heigh1' || id == 'heigh1'){
            ARR[k].v = ARR[k].v.replace('厘米','');
            ARR[k].v = ARR[k].v.replace('cm','');
        }
        var A = zeai.addtag('a');
        A.innerHTML = ARR[k].v;
        A.onclick = function (){
            ZEAI_delclass(so_li.getElementsByTagName("a"));
            this.className = 'ed';span.innerHTML = ARR[k].v;
            o(id).value = ARR[k].i;so_li.hide();
            span.style.color='#333';
        }
        if (ARR[k].i == formid.value){A.className = 'ed';span.innerHTML = ARR[k].v;/*span.style.color='#333'*/;}
        so_li.appendChild(A);
    })(k);}
    so_id.onmouseover = function (){so_li.show();}
    so_id.onmouseout = function (){so_li.hide();}
}



function ZEAI_birthday(obj) {
    var ul=obj.ul,bx=obj.bx;
    selstr =(zeai.empty(obj.selstr))?selstr2:obj.selstr;
    var span=ul.children[0],li=ul.children[1];
    span.html(selstr);
    li.children[0].style.width=(parseInt(ul.offsetWidth)-2)+'px';
    var Ybox=o(ul.id+'Ybox'),Mbox=o(ul.id+'Mbox'),Dbox=o(ul.id+'Dbox');

    var defdate=obj.defdate;
    var defY,defM,defD;

    ul.onmouseleave=function(){
        if(span.innerHTML!=selstr)span.style.color='#333';
        li.hide();
    }
    ul.onmouseenter=function(){
        li.show();
    }
    if(!zeai.empty(defdate)){
        span.html(defdate);
        defdateARR = defdate.split('-');
        defY=defdateARR[0],defM=defdateARR[1],defD=defdateARR[2];
        Ybox.append(YboxFN());
        Mbox.append(MboxFN(defY));
        Dbox.append(DboxFN(defY,defM));
        span.html(defdate);
        Ybox.hide();Mbox.hide();Dbox.show();
        span.style.color='#333';
    }else{
        Ybox.append(YboxFN());Ybox.show();
        defY=0,defM=0,defD=0;
    }

    function YboxFN() {
        var nowyear=new Date().getFullYear();
        Ybox.append(Ylist(2000,nowyear-18,'00后：'));
        Ybox.append(Ylist(1990,1999,'90后：'));
        Ybox.append(Ylist(1980,1989,'80后：'));
        Ybox.append(Ylist(1970,1979,'70后：'));
        Ybox.append(Ylist(1960,1969,'60后：'));
        Ybox.append(Ylist(1950,1959,'50后：'));
        Ybox.append(Ylist(1940,1949,'40后：'));
        function Ylist(i1,i2,title) {
            var p=zeai.addtag('p'),b=zeai.addtag('b');
            b.html(title);
            p.append(b);
            for(y=i1;y<=i2;y++) {
                (function(y){
                    var i=zeai.addtag('i');i.html(y);
                    i.onclick = function(){
                        Mbox.html('');
                        Mbox.append(MboxFN(y));
                        Ybox.hide();Mbox.show();Dbox.hide();
                        setClass(Ybox,y);this.class('ed');
                    }
                    if(y==defY)i.class('ed');
                    p.append(i);
                })(y);

            }
            return p;
        }
    }
    //调出月份
    function MboxFN(yV) {
        var marr=[1,2,3,4,5,6,7,8,9,10,11,12];
        var p=zeai.addtag('p');
        //返回
        var font=zeai.addtag('font');
        font.html(yV+'年');font.title='点击选择年份';font.class('ed');
        font.onclick=function(){Ybox.show();Mbox.hide();Dbox.hide();}
        p.append(font);
        //增加月列表
        zeai.listEach(marr,function(m,n){
            var i=zeai.addtag('i');i.html(m);
            i.onclick=function(){
                Dbox.html('');
                Dbox.append(DboxFN(yV,m));
                Ybox.hide();Mbox.hide();Dbox.show();
                setClass(Mbox,m);this.class('ed');
            }
            if(m==defM)i.class('ed');
            p.append(i);

        });
        span.html('请选择月');
        return p;
    }
    //调出日
    function DboxFN(y,m){
        span.html('请选择日');
        var darr=ZeaiPC.day(y,m);
        var p=zeai.addtag('p');
        //返回
        var fontY=zeai.addtag('font'),fontM=zeai.addtag('font');
        fontY.html(y+'年');fontY.title='点击选择年份';fontY.class('ed');
        fontM.html(m+'月');fontM.title='点击选择月份';fontM.class('ed');
        fontY.onclick=function(){Ybox.show();Mbox.hide();Dbox.hide();}
        fontM.onclick=function(){Ybox.hide();Mbox.show();Dbox.hide();}
        p.append(fontY);p.append(fontM);
        zeai.listEach(darr,function(d,n){
            var i=zeai.addtag('i');i.html(d);i.title='点击确定';
            i.onclick=function(){
                o('birthday').value=y+'-'+m+'-'+d
                span.html(o('birthday').value);
                setClass(Dbox,d);this.class('ed');
                li.hide();
            }

            if(d==defD)i.class('ed');
            p.append(i);
        });
        return p;
    }
}
function setClass(box,num){zeai.listEach(zeai.tag(box,'i'),function(obj){obj.class('');});}
function ZEAI_height(ulstr,form){
    var span=o(ulstr).children[0];
    o(ulstr).onmouseleave=function(){o(ulstr).children[1].hide();}
    o(ulstr).onmouseenter=function(){o(ulstr).children[1].show();}
    zeai.listEach(heigh_ARR,function(obj){
        var i=zeai.addtag('i');i.html(obj.i);
        if(o(form).value == obj.i){span.html(obj.v);i.class('ed');span.style.color='#333';}
        i.onclick=function(){
            o(ulstr+'box').parentNode.hide();
            o(form).value=obj.i;
            setClass(o(ulstr+'box'),obj.i);this.class('ed');
            span.html(obj.v);
            span.style.color='#333';
        }
        o(ulstr+'box').append(i);
    });
}